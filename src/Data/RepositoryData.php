<?php

namespace IBroStudio\Git\Data;

use IBroStudio\DataRepository\ValueObjects\GitSshUrl;
use IBroStudio\Git\Contracts\GitProviderContract;
use IBroStudio\Git\Data\RepositoryData\ConfigData\BranchData;
use IBroStudio\Git\Data\RepositoryData\ConfigData\RemoteData;
use IBroStudio\Git\Data\RepositoryData\RepositoryConfigData;
use IBroStudio\Git\Enums\GitProvidersEnum;
use IBroStudio\Git\Enums\GitRepositoryVisibilitiesEnum;
use IBroStudio\Git\Exceptions\GitRepositoryException;
use IBroStudio\Git\Integrations\Github\GithubResponse;
use IBroStudio\Ploi\SDK\PloiResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Saloon\Http\Response;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class RepositoryData extends Data
{
    #[Computed]
    public string $path;

    #[Computed]
    public string $fullname;

    public function __construct(
        public string $name,
        public BranchData|string $branch,
        public GitProvidersEnum $provider,
        public RemoteData $remote,
        public string|Optional $localParentDirectory,
    ) {}

    public static function fromPath(string $path): self
    {
        $config = RepositoryConfigData::from($path);

        return new self(
            name: $config->remote->name,
            branch: $config->branch,
            provider: GitProvidersEnum::from($config->remote->url->provider),
            remote: $config->remote,
            localParentDirectory: Str::beforeLast($path, '/'),
        );
    }

    public static function fromGithub(GithubResponse $response): self
    {
        $data = $response->json();

        return new self(
            name: $data['name'],
            branch: BranchData::from($response),
            provider: GitProvidersEnum::GITHUB,
            remote: RemoteData::from($response),
            localParentDirectory: Optional::create(),
        );
    }

    public static function collectFromGithub(GithubResponse $response): array
    {
        return Arr::map($response->json(), function (array $data) use ($response) {
            return new self(
                name: $data['name'],
                branch: BranchData::from($response, $data),
                provider: GitProvidersEnum::GITHUB,
                remote: RemoteData::from($response, $data),
                localParentDirectory: Optional::create(),
            );
        });
    }

    public function refresh(): self
    {
        return self::from($this->path);
    }

    private function _provider(): self
    {
        if ($this->provider instanceof GitProvidersEnum) {
            $this->provider = app(GitProviderContract::class)
                ->get($this->provider->value);
        }

        return $this;
    }

    private function _ssh_url(): self
    {
        if (is_string($this->ssh_url)) {
            $this->ssh_url = GitSshUrl::from($this->ssh_url);
        }

        if (is_array($this->ssh_url) && Arr::has($this->ssh_url, 'url')) {
            $this->ssh_url = GitSshUrl::from($this->ssh_url['url']);
        }

        return $this;
    }

    private function _owner(): self
    {
        if (is_array($this->owner)) {
            $this->owner = $this->owner['login'];
        }

        return $this;
    }

    private function _path(): self
    {
        if (! is_null($this->localParentDirectory)) {
            $this->path = Str::of($this->localParentDirectory)
                ->when(! Str::endsWith($this->localParentDirectory, '/'), function (Stringable $string) {
                    return $string->append('/');
                })
                ->append($this->name);
        }

        return $this;
    }

    private function _remote(): self
    {
        if (is_null($this->remote)) {
            $this->remote = config('git.default.remote');
        }

        return $this;
    }

    private function _fullname(): self
    {
        $this->fullname = "{$this->owner}/{$this->name}";

        return $this;
    }
}
