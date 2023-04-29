<?php

namespace IBroStudio\Git\Providers\Github;

use Carbon\Carbon;
use GrahamCampbell\GitHub\GitHubManager;
use IBroStudio\Git\Commit;
use IBroStudio\Git\DtO\CommitData;
use IBroStudio\Git\DtO\RepositoryData;
use IBroStudio\Git\Exceptions\GitException;
use IBroStudio\Git\Providers\GithubProvider;
use IBroStudio\Git\Repository;
use IBroStudio\Git\Tag;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class GithubCommit
{
    private static $instance;

    private function __construct(public GithubRepository $repository) {}

    public static function getInstance(GithubRepository $repository)
    {
        if (!self::$instance) {
            self::$instance = new self($repository);
        }

        return self::$instance;
    }

    public function last(): CommitData
    {
        $commit = $this->repository
            ->properties
            ->provider
            ->api
            ->repo()
            ->commits()
            ->all(
                username: $this->repository->properties->owner,
                repository: $this->repository->properties->name,
                params: [
                    'sha' => $this->repository->properties->branch
                ]
            )[0];

        return CommitData::from([
            'hash' => $commit['sha'],
            'title' => $commit['commit']['message'],
            'author' => $commit['commit']['author']['name'],
            'email' => $commit['commit']['author']['email'],
            'date' => Carbon::parse($commit['commit']['author']['date'], 'UTC'),
        ]);
    }
}
