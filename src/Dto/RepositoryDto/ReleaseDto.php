<?php

declare(strict_types=1);

namespace IBroStudio\Git\Dto\RepositoryDto;

use Carbon\CarbonImmutable;
use IBroStudio\DataObjects\ValueObjects\SemanticVersion;
use IBroStudio\Git\Integrations\Github\GithubResponse;
use IBroStudio\Tasks\DTO\DefaultProcessPayloadDTO;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Optional;

class ReleaseDto extends DefaultProcessPayloadDTO
{
    public function __construct(
        public SemanticVersion $version,
        public CommitDto|Optional $commit,
        public ?SemanticVersion $previous = null,
        public ?CarbonImmutable $published_at = null,
        public array $gitLogBoundaries = [],
        public ?string $description = null,
    ) {
        if (! is_null($this->previous)
            && $this->previous->withoutPrefix() !== '0.0.0'
            && is_null($this->published_at)
        ) {
            $this->gitLogBoundaries = ['from' => $this->previous->value, 'to' => 'HEAD'];
        }

        if (! is_null($this->previous) && ! is_null($this->published_at)) {
            $this->gitLogBoundaries = ['from' => $this->previous->value, 'to' => $this->version->value];
        }

        if (is_null($this->published_at)) {
            $this->published_at = new CarbonImmutable;
        }
    }

    public static function fromGithub(GithubResponse $response): self
    {
        $data = $response->json();

        return self::from([
            'version' => $data['tag_name'],
            'published_at' => $data['published_at'],
        ]);
    }

    public static function collectFromGithub(GithubResponse $response): array
    {
        $data = $response->json();
        $releases = Collection::make();

        collect($data)
            ->sliding(2)
            ->each(function (Collection $release) use (&$releases) {
                $row = $release->first();
                $releases->push(
                    self::from([
                        'version' => $row['tag_name'],
                        'published_at' => $row['published_at'],
                        'previous' => $release->last()['tag_name'],
                    ])
                );
            });

        $first = end($data);
        $releases->push(
            self::from([
                'version' => $first['tag_name'],
                'published_at' => $first['published_at'],
            ])
        );

        return $releases->all();
    }
}
