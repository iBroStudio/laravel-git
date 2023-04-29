<?php

namespace IBroStudio\Git\Providers;

use GrahamCampbell\GitHub\GitHubManager;
use IBroStudio\Git\Contracts\ProviderContract;
use IBroStudio\Git\Git;
use IBroStudio\Git\Providers\Github\GithubCommit;
use IBroStudio\Git\Providers\Github\GithubRelease;
use IBroStudio\Git\Providers\Github\GithubRepository;
use IBroStudio\Git\Repository;
use IBroStudio\ReleaseManager\Contracts\ReleaseHandlerContract;
use IBroStudio\ReleaseManager\Contracts\VersionFormatterContract;
use IBroStudio\ReleaseManager\Contracts\VersionManagerContract;
use IBroStudio\ReleaseManager\DtO\NewReleaseData;
use IBroStudio\ReleaseManager\DtO\ReleaseData;
use IBroStudio\ReleaseManager\DtO\RepositoryData;
use IBroStudio\ReleaseManager\DtO\VersionData;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class GithubProvider implements ProviderContract
{
    public function __construct(
        public GitHubManager $api
    ) {}

    public function repository()
    {
        return GithubRepository::getInstance($this);
    }

    public function commits()
    {
        return GithubCommit::getInstance($this->repository());
    }

    public function releases(Repository $repository)
    {
        return GithubRelease::getInstance($repository);
    }
}
