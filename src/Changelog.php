<?php

namespace IBroStudio\Git;

use IBroStudio\DataRepository\ValueObjects\SemanticVersion;
use IBroStudio\Git\Contracts\ChangelogContract;
use IBroStudio\Git\Data\ChangelogConfigData;
use IBroStudio\Git\Data\GitCommitData;
use IBroStudio\Git\Data\GitReleaseData;
use IBroStudio\Git\Enums\GitCommitTypes;
use IBroStudio\Git\Exceptions\ChangelogException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

final class Changelog implements ChangelogContract
{
    public const CONTENT_HEADER_KEY = 'header';

    public const CONTENT_SECTIONS_KEY = 'sections';

    public const SPACE = ' ';

    protected GitRepository $repository;

    protected ChangelogConfigData $config;

    protected string $changelogFile;

    public Collection $content;

    protected Collection $section;

    public function __construct()
    {
        $this->config = ChangelogConfigData::from(config('git.changelog'));
    }

    public function bind(GitRepository $repository): self
    {
        $this->repository = $repository;

        $this->changelogFile = $this->repository->properties->path.'/'.$this->config->file;

        $this->initContent();

        $this->load();

        return $this;
    }

    public function prepend(GitReleaseData $releaseData): bool
    {
        $this->getSection(self::CONTENT_SECTIONS_KEY)
            ->prepend(
                value: $this->buildSection($releaseData),
                key: $releaseData->version->withoutPrefix()->value()
            );

        return $this->save();
    }

    public function pick(SemanticVersion $version): ?Collection
    {
        return $this->getSection(self::CONTENT_SECTIONS_KEY)
            ->get($version->withoutPrefix()->value());
    }

    public function rebuild(): bool
    {
        $this->initContent();

        $this->repository->release()->all()
            ->each(function (GitReleaseData $releaseData) {
                $this->addToContentSections(
                    $releaseData->gitLogBoundaries['to'] ?? $releaseData->version->withoutPrefix()->value(),
                    $this->buildSection($releaseData)
                );
            });

        return $this->save();
    }

    protected function getSection(string $key): ?Collection
    {
        return $this->content->get($key);
    }

    protected function addToContentSections($key, $value): void
    {
        $this->getSection(self::CONTENT_SECTIONS_KEY)
            ->put($key, $value);
    }

    protected function buildSection(GitReleaseData $releaseData): Collection
    {
        $section = collect([
            'header' => Str::of(config('git.changelog.version_header_tag'))
                ->append(self::SPACE)
                ->append(
                    Str::of($releaseData->version->withoutPrefix()->value())
                        ->when(isset($releaseData->gitLogBoundaries),
                            function (Stringable $string) use ($releaseData) {
                                return $string
                                    ->wrap('[', ']')
                                    ->append(
                                        Str::of($releaseData->gitLogBoundaries['from'])
                                            ->append('...')
                                            ->append($releaseData->gitLogBoundaries['to'])
                                            ->prepend($this->repository->properties->html_url.'/compare/')
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
            $this->repository->commit()->history(...$releaseData->gitLogBoundaries)
                ->each(function (GitCommitData $commit) use ($section) {
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
                if ($type !== GitCommitTypes::UNLISTED->value && $commits->count() < 2) {
                    $section
                        ->get('commits')
                        ->pull($type);
                }
            });

        return $section;
    }

    protected function formatSectionLine(GitCommitData $commit): string
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
                            ->prepend($this->repository->properties->html_url.'/commit/')
                            ->wrap('(', ')')
                    )
                    ->wrap('(', ')')
            )
            ->toString();
    }

    protected function initContent(): void
    {
        $this->content = collect([
            self::CONTENT_HEADER_KEY => $this->config->header,
            self::CONTENT_SECTIONS_KEY => Collection::make(),
        ]);
    }

    protected function load(): bool
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

    protected function save(): bool
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
