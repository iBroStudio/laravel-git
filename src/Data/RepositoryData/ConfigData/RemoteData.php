<?php

namespace IBroStudio\Git\Data\RepositoryData\ConfigData;

use IBroStudio\DataRepository\ValueObjects\GitSshUrl;
use IBroStudio\Git\Integrations\Github\GithubResponse;
use Illuminate\Support\Arr;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class RemoteData extends Data
{
    public function __construct(
        public string|Optional $name,
        public GitSshUrl|string $url,
        public string|Optional $fetch,
    ) {
        $this->_url();
    }

    public static function fromConfig(array $config): self
    {
        $name = key($config);

        return new self(
            name: key($config),
            url: GitSshUrl::from($config[$name]['url']),
            fetch: $config[$name]['fetch'],
        );
    }

    public static function fromGithub(GithubResponse $response, ?array $data = null): self
    {
        $data = $data ?? $response->json();

        return new self(
            name: Optional::create(),
            url: Arr::get($data, 'ssh_url'),
            fetch: Optional::create(),
        );
    }

    private function _url(): self
    {
        if (is_string($this->url)) {
            $this->url = GitSshUrl::from($this->url);
        }

        return $this;
    }
}
