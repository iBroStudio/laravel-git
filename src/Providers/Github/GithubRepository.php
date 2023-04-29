<?php

namespace IBroStudio\Git\Providers\Github;

use GrahamCampbell\GitHub\GitHubManager;
use IBroStudio\Git\Commit;
use IBroStudio\Git\DtO\RepositoryData;
use IBroStudio\Git\Exceptions\GitException;
use IBroStudio\Git\Providers\GithubProvider;
use IBroStudio\Git\Repository;
use IBroStudio\Git\Tag;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class GithubRepository
{
    private static $instance;

    private function __construct(public GithubProvider $provider) {}

    public static function getInstance(GithubProvider $provider)
    {
        if (!self::$instance) {
            self::$instance = new self($provider);
        }

        return self::$instance;
    }

    public function get(string $username, string $repository, string $path): RepositoryData
    {
        return RepositoryData::from(
            $this->provider,
            $this->provider->api
                ->repo()
                ->show(
                    username: $username,
                    repository: $repository
                ),
            $path
        );
    }
}
