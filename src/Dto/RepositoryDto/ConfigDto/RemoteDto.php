<?php

declare(strict_types=1);

namespace IBroStudio\Git\Dto\RepositoryDto\ConfigDto;

use IBroStudio\DataObjects\ValueObjects\GitSshUrl;
use IBroStudio\Git\Dto\RepositoryDto\RepositoryLocalPropertiesDto;
use IBroStudio\Git\Integrations\Github\GithubResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class RemoteDto extends Data
{
    public function __construct(
        public string|Optional $name,
        public GitSshUrl|Optional $url,
        public string|Optional $fetch,
        public ?string $html = null,
    ) {
        if ($this->url instanceof GitSshUrl) {
            $this->html = $this->url->toHttp();
        }
    }

    public static function fromLocalProperties(RepositoryLocalPropertiesDto $properties): self
    {
        $name = key($properties->remote);

        return self::from([
            'name' => $name,
            'url' => $properties->remote[$name]['url'],
            'fetch' => $properties->remote[$name]['fetch'],
        ]);
    }

    public static function fromGithub(GithubResponse $response, ?array $data = null): self
    {
        $data = $data ?? $response->json();

        return self::from([
            'name' => Optional::create(),
            'url' => Arr::get($data, 'ssh_url'),
            'fetch' => Optional::create(),
        ]);
    }

    public function toHtml(?string $path = null): string
    {
        return Str::of($this->html)
            ->when(! is_null($path), function (Stringable $html) use ($path) {
                return $html
                    ->chopEnd('/')
                    ->append('/')
                    ->append($path);
            })
            ->value();
    }
}
