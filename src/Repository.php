<?php

declare(strict_types=1);

namespace IBroStudio\Git;

use IBroStudio\DataObjects\Casts\DtoContractCastTransformer;
use IBroStudio\DataObjects\Concerns\UpdatableDto;
use IBroStudio\DataObjects\Enums\GitProvidersEnum;
use IBroStudio\DataObjects\ValueObjects\GitSshUrl;
use IBroStudio\Git\Concerns\RepositoryInstantiators;
use IBroStudio\Git\Concerns\RepositorySubResources;
use IBroStudio\Git\Contracts\CommittableContract;
use IBroStudio\Git\Contracts\OwnerDtoContract;
use IBroStudio\Git\Contracts\RepositoryResourceContract;
use IBroStudio\Git\Dto\RepositoryDto\CommitDto;
use IBroStudio\Git\Dto\RepositoryDto\ConfigDto\BranchDto;
use IBroStudio\Git\Dto\RepositoryDto\ConfigDto\RemoteDto;
use IBroStudio\Git\Dto\RepositoryDto\NewDto;
use IBroStudio\Git\Enums\GitRepositoryVisibilitiesEnum;
use IBroStudio\Git\Exceptions\InvalidGitRepositoryPathException;
use IBroStudio\Git\Processes\InitRepositoryProcess;
use IBroStudio\NeonConfig\Concerns\UseNeonConfig;
use IBroStudio\Tasks\Contracts\PayloadContract;
use IBroStudio\Tasks\DTO\ProcessableDto;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Spatie\LaravelData\Attributes\WithCastAndTransformer;
use Spatie\LaravelData\Optional;

class Repository extends ProcessableDto implements CommittableContract, PayloadContract
{
    use RepositoryInstantiators;
    use RepositorySubResources;
    use UpdatableDto;
    use UseNeonConfig;

    public readonly string $identifier;

    private RepositoryResourceContract $api;

    final public function __construct(
        public string $name,
        public BranchDto|string $branch,
        public GitProvidersEnum $provider,
        public RemoteDto $remote,
        #[WithCastAndTransformer(DtoContractCastTransformer::class)]
        public OwnerDtoContract|string|Optional $owner,
        public string|Optional $localParentDirectory,
        public GitRepositoryVisibilitiesEnum|Optional $visibility,
        public NewDto|Optional $new,
        public CommitDto|Optional $commit,
        public array $topics = [],
        public ?string $path = null,
        public ?GitSshUrl $template = null,
    ) {
        $this->_path();

        $this->identifier = "{$this->remote->url->username}-{$this->remote->url->repository}";

        if (! is_null($this->path)) {
            $this->handleNeon('laravel-git.neon', $this->path)
                ->forConfig($this->identifier);

            Config::set($this->identifier, array_merge(
                Config::get('git'),
                Config::get($this->identifier) ?? []
            ));
        }
    }

    public static function clone(
        GitSshUrl|string $url,
        string $localParentDirectoryPath,
        ?string $localRepositoryDirectoryName = null): self
    {
        if (! $realpath = realpath($localParentDirectoryPath)) {
            throw new InvalidGitRepositoryPathException($localParentDirectoryPath);
        }

        if (is_string($url)) {
            $url = GitSshUrl::from($url);
        }

        $command = Str::of('git clone '.$url->value)
            ->when($localRepositoryDirectoryName, function (Stringable $string) use ($localRepositoryDirectoryName) {
                return $string->append(' '.$localRepositoryDirectoryName);
            })
            ->value();

        Process::path($realpath)
            ->run($command)
            ->throw();

        return self::from($realpath.'/'.$url->repository);
    }

    public static function init(array $data): self
    {
        /** @var OwnerDtoContract $owner */
        $owner = Arr::get($data, 'owner', config('git.default.owner.type')::from(['name' => config('git.default.owner.name')]));
        $provider = Arr::get($data, 'provider', config('git.default.provider'));

        $data = array_merge(
            [
                'branch' => Arr::get($data, 'branch', config('git.default.branch')),
                'owner' => $owner,
                'provider' => $provider,
                'remote' => Arr::get($data, 'remote', RemoteDto::from([
                    'name' => config('git.default.remote'),
                    'url' => GitSshUrl::build($provider, $owner->name, $data['name']),
                ])),
                'localParentDirectory' => $data['localParentDirectory'],
                'visibility' => Arr::get($data, 'visibility', GitRepositoryVisibilitiesEnum::PRIVATE),
            ],
            $data
        );

        $repository = self::from($data);

        /** @var InitRepositoryProcess $process */
        $process = InitRepositoryProcess::create([
            'payload' => $repository,
        ])->handle();

        return self::open($process->payload->path);
    }

    public static function open(string $path): self
    {
        if (! $realpath = realpath($path)) {
            throw new InvalidGitRepositoryPathException($path);
        }

        return self::from($realpath);
    }

    public function isPrivate(): bool
    {
        return $this->visibility === GitRepositoryVisibilitiesEnum::PRIVATE;
    }

    public function api(): RepositoryResourceContract
    {
        if (isset($this->api)) {
            return $this->api;
        }

        return $this->api = Git::use($this->provider)->repository($this);
    }

    public function config(?string $key = null): mixed
    {
        $config = config($this->identifier);

        return $key ? Arr::get($config, $key) : $config;
    }

    public function hasChanges(): bool
    {
        return ! empty($this->status());
    }

    public function status(): string
    {
        Process::path($this->path)
            ->run('git update-index -q --refresh')
            ->throw();

        return Process::path($this->path)
            ->run('git status --porcelain')
            ->output();
    }

    public function fetch(): self
    {
        Process::path($this->path)
            ->run("git fetch {$this->remote->name} --tags")
            ->throw();

        return $this;
    }

    public function pull(): self
    {
        Process::path($this->path)
            ->run(
                Arr::join(['git pull --rebase', $this->remote->name, $this->branch->name], ' ')
            )
            ->throw();

        return $this;
    }

    public function push(): self
    {
        Process::path($this->path)
            ->run(
                Arr::join(['git push', $this->remote->name, $this->branch->name], ' ')
            )
            ->throw();

        return $this;
    }

    public function restore(): bool
    {
        Process::path($this->path)
            ->run('git restore . --worktree --staged')
            ->throw();

        return true;
    }

    private function _path(): self
    {
        if (! $this->localParentDirectory instanceof Optional) {
            $this->path = Str::of($this->localParentDirectory)
                ->when(! Str::endsWith($this->localParentDirectory, '/'), function (Stringable $string) {
                    return $string->append('/');
                })
                ->append($this->name)
                ->value();
        }

        return $this;
    }
}
