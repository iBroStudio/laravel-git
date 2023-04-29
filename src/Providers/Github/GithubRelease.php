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

class GithubRelease
{
    private static $instance;

    private function __construct(public Repository $repository) {}

    public static function getInstance(Repository $repository)
    {
        if (!self::$instance) {
            self::$instance = new self($repository);
        }

        return self::$instance;
    }

    public function all(): array
    {
        return $this->repository
            ->properties
            ->provider
            ->api
            ->repo()
            ->releases()
            ->all(
                username: $this->repository->properties->owner,
                repository: $this->repository->properties->name
            );
    }
}
