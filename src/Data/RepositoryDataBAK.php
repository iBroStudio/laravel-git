<?php

namespace IBroStudio\Git\Data;

use IBroStudio\DataRepository\ValueObjects\GitSshUrl;
use IBroStudio\Git\Contracts\GitProviderContract;
use IBroStudio\Git\Enums\GitProvidersEnum;
use IBroStudio\Git\Enums\GitRepositoryVisibilitiesEnum;
use IBroStudio\Git\Exceptions\GitRepositoryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;

class RepositoryDataBAK extends Data
{
    #[Computed]
    public string $path;

    #[Computed]
    public string $fullname;

    public function __construct(
        public string                                    $name,
        #[MapInputName('default_branch')]
        public string                                    $branch,
        #[WithCast(EnumCast::class)]
        public string|array                              $owner,
        public GitProviderContract|GitProvidersEnum|null $provider = null,
        public ?string                                   $remote = null,
        public ?string                                   $localParentDirectory = null,
        public GitSshUrl|string|array|null               $ssh_url = null,
        #[WithCast(EnumCast::class)]
        public ?GitRepositoryVisibilitiesEnum            $visibility = null,
        public ?string                                   $templateOwner = null,
        public ?string                                   $templateRepo = null,
        public ?string                                   $html_url = null,
    ) {
        $this
            ->_provider()
            ->_ssh_url()
            ->_remote()
            ->_owner()
            ->_path()
            ->_fullname();
    }

    public static function fromMultiple(string $path, ?GitProviderContract $provider = null): self
    {
        /*
                $config = Process::path($path)
                ->run('git branch -a')
                ->throw();
                dd($config->output());

                *
                main\n
                remotes/origin/HEAD -> origin/main\n
                remotes/origin/main\n

                branch.main.remote=origin\n
                branch.main.merge=refs/heads/main\n


        core.repositoryformatversion=0\n
core.filemode=true\n
core.bare=false\n
core.logallrefupdates=true\n
core.ignorecase=true\n
core.precomposeunicode=true\n
remote.origin.url=git@github.com:Yann-iBroStudio/jahazzz.github.com\n
remote.origin.fetch=+refs/heads/*:refs/remotes/origin/*\n
branch.master.remote=origin\n
branch.master.merge=refs/heads/master\n

                */
        $config = Process::path($path)
            ->run('git config --local -l')
            ->throw();
        //dd($config->output());

        $data2 = [];
        $data = Str::of($config->output())
            ->split('/\n/')
            ->mapWithKeys(function (string $line) use (&$data2) {
                $split = Str::of($line)->split('/=/');
                return data_set($data2, $split->first(), $split->last(), overwrite: false);
                return [$split->first() => $split->last()];
            });
        //branch = dd(key($data->get('branch')));
        dd($data);
        $properties = [];

        collect(explode("\n", $config->output()))
            ->filter(function (string $line) {
                return Str::contains($line, 'remote.origin.url')
                    || Str::containsAll($line, ['branch', 'remote=origin']);
            })
            ->each(function (string $line) use (&$properties, $provider) {
                if (
                    preg_match(
                        '/(?<provider>.*)\..*(\/|:)(?<owner>.*)\/(?<repository>.*)/',
                        Str::of($line)
                            ->after('@')
                            ->after('https://')
                            ->whenEndsWith('.git', function (Stringable $string) {
                                return $string->beforeLast('.git');
                            })
                            ->__toString(),
                        $matches
                    )
                ) {

                    $properties = [
                        'name' => $matches['repository'],
                        'remote' => 'origin',
                        'ssh_url' => Str::after($line, '='),
                        'provider' => $provider ??
                            app(GitProviderContract::class)
                                ->get(GitProvidersEnum::from($matches['provider'])->value),
                        'owner' => $matches['owner'],
                    ];
                }

                if (
                    preg_match(
                        '/branch\.(?<branch>.*)\.remote=origin/',
                        $line,
                        $matches
                    )
                ) {
                    $properties['branch'] = $matches['branch'];
                }
            });

        if (! count($properties)) {
            throw new GitRepositoryException('Missing repository remote', $path);
        }
dd($properties);
        $properties = new self(
            name: $properties['name'],
            branch: $properties['branch'],
            owner: $properties['owner'],
            provider: $properties['provider'],
            remote: $properties['remote'],
            localParentDirectory: Str::beforeLast($path, '/'),
            ssh_url: $properties['ssh_url'],
        );

        return $properties->provider
            ->repository($properties)
            ->get()
            ->properties();
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
