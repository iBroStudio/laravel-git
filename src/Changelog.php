<?php

declare(strict_types=1);

namespace IBroStudio\Git;

use IBroStudio\DataObjects\ValueObjects\SemanticVersion;
use IBroStudio\Git\Dto\ChangelogConfigDto;
use IBroStudio\Git\Dto\RepositoryDto\CommitDto;
use IBroStudio\Git\Dto\RepositoryDto\ReleaseDto;
use IBroStudio\Git\Enums\CommitTypeEnum;
use IBroStudio\Git\Exceptions\ChangelogException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

class Changelog
{
    public const CONTENT_HEADER_KEY = 'header';

    public const CONTENT_SECTIONS_KEY = 'sections';

    public const SPACE = ' ';

    public Collection $content;

    private ChangelogConfigDto $config;

    private string $changelogFile;

    private Collection $section;

    public function __construct(protected Repository $repository)
    {
        $this->config = ChangelogConfigDto::from($this->repository->config('changelog'));

        $this->changelogFile = $this->repository->path.'/'.$this->config->file;

        $this->initContent();

        $this->load();
    }

    public function prepend(ReleaseDto $releaseData): bool
    {
        $this->getSection(self::CONTENT_SECTIONS_KEY)
            ->prepend(
                value: $this->buildSection($releaseData),
                key: $releaseData->version->withoutPrefix()
            );

        return $this->save();
    }

    public function pick(SemanticVersion|string $version): ?Collection
    {
        if (is_string($version)) {
            $version = SemanticVersion::from($version);
        }

        return $this->getSection(self::CONTENT_SECTIONS_KEY)
            ->get($version->withoutPrefix());
    }

    public function describe(SemanticVersion|string $version): ?string
    {
        if (is_string($version)) {
            $version = SemanticVersion::from($version);
        }

        $lines = $this->pick($version)->get('lines');

        if (is_null($lines)) {
            return null;
        }

        return implode(
            "\n",
            $lines->filter(function (string $line) {
                return ! empty($line) && $line !== config('git.changelog.section_delimiter');
            })
                ->flatten()
                ->toArray()
        );
    }

    public function rebuild(): bool
    {
        $this->initContent();

        $this->repository->releases()->all()
            ->each(function (ReleaseDto $releaseData) {
                $this->addToContentSections(
                    $releaseData->gitLogBoundaries['to'] ?? $releaseData->version->withoutPrefix(),
                    $this->buildSection($releaseData)
                );
            });

        return $this->save();
    }

    private function getSection(string $key): ?Collection
    {
        return $this->content->get($key);
    }

    private function addToContentSections($key, $value): void
    {
        $this->getSection(self::CONTENT_SECTIONS_KEY)
            ->put($key, $value);
    }

    private function buildSection(ReleaseDto $releaseData): Collection
    {
        $section = collect([
            'header' => Str::of(config('git.changelog.version_header_tag'))
                ->append(self::SPACE)
                ->append(
                    Str::of($releaseData->version->withoutPrefix())
                        ->when(count($releaseData->gitLogBoundaries),
                            function (Stringable $string) use ($releaseData) {
                                return $string
                                    ->wrap('[', ']')
                                    ->append(
                                        Str::of($releaseData->gitLogBoundaries['from'])
                                            ->append('...')
                                            ->append($releaseData->gitLogBoundaries['to'])
                                            ->prepend($this->repository->remote->toHtml('compare'))
                                            ->wrap('(', ')')
                                    );
                            })
                )
                ->append(
                    Str::of($releaseData->published_at->format('Y-m-d'))
                        ->wrap(' (', ')')
                )
                ->toString(),

            'commits' => collect(config('git.changelog.included_types'))
                ->mapWithKeys(function ($type) {
                    return [
                        $type->value => $type->label() ?
                            collect([
                                Str::of($type->label())
                                    ->prepend(self::SPACE)
                                    ->prepend(config('git.changelog.type_header_tag'))
                                    ->toString(),
                            ])
                            : Collection::make(),
                    ];
                }),
            'footer' => Str::of(config('git.changelog.section_delimiter'))
                ->wrap("\n")
                ->toString(),
        ]);

        if (isset($releaseData->gitLogBoundaries)) {

            $this->repository->fetch();

            $this->repository->commits()->history(...$releaseData->gitLogBoundaries)
                ?->each(function (CommitDto $commit) use ($section) {
                    $section
                        ->get('commits')
                        ->get($commit->type->value)
                        ->push(
                            $this->formatSectionLine($commit)
                        );
                });
        }

        $section->get('commits')
            ->each(function (Collection $commits, string $type) use ($section) {
                if ($type !== CommitTypeEnum::UNLISTED->value && $commits->count() < 2) {
                    $section
                        ->get('commits')
                        ->pull($type);
                }
            });

        return $section;
    }

    private function formatSectionLine(CommitDto $commit): string
    {
        return Str::of('* ')
            ->append($commit->message)
            ->append(self::SPACE)
            ->append(
                Str::of($commit->hash)
                    ->take(6)
                    ->wrap('[', ']')
                    ->append(
                        Str::of($commit->hash)
                            ->prepend($this->repository->remote->toHtml('commit'))
                            ->wrap('(', ')')
                    )
                    ->wrap('(', ')')
            )
            ->toString();
    }

    private function initContent(): void
    {
        $this->content = collect([
            self::CONTENT_HEADER_KEY => $this->config->header,
            self::CONTENT_SECTIONS_KEY => Collection::make(),
        ]);
    }

    private function load(): bool
    {
        if (! File::exists($this->changelogFile)) {
            return false;
        }

        $file = File::get($this->changelogFile);

        $content = Str::of($file)->explode("\n");

        $content->each(function ($line) {

            if (Str::startsWith($line, config('git.changelog.version_header_tag').self::SPACE)) {
                $version = Str::of($line)->match('/\[(v?[0-9]+\.[0-9]+\.[0-9]+)\]/');
                $this->section = collect([
                    'header' => $line,
                    'lines' => Collection::make(),
                ]);

                $this->addToContentSections($version->toString(), $this->section);

                return true;
            }

            return ! isset($this->section) || $this->section->get('lines')->push($line);
        });

        return true;
    }

    private function save(): bool
    {
        if (
            ! File::put(
                path: $this->changelogFile,
                contents: implode("\n", $this->content->flatten()->toArray())
            )
        ) {
            throw new ChangelogException("Cannot save {$this->changelogFile}");
        }

        return true;
    }
}
