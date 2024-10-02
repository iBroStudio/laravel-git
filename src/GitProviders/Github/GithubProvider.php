<?php

namespace IBroStudio\Git\GitProviders\Github;

use Github\ResultPager;
use GrahamCampbell\GitHub\GitHubManager;
use IBroStudio\Git\Contracts\GitProviderContract;
use IBroStudio\Git\Enums\GitProvidersEnum;
use IBroStudio\Git\GitProviders\GitProvider;

class GithubProvider extends GitProvider implements GitProviderContract
{
    protected ResultPager $paginator;

    public function __construct(
        public readonly GitHubManager $api,
        protected GitProvidersEnum $providerKey = GitProvidersEnum::GITHUB
    ) {
        parent::__construct($providerKey);

        $this->paginator = new ResultPager($api->connection());
    }

    public function api(): mixed
    {
        return $this->api;
    }

    public function paginator(): ResultPager
    {
        return $this->paginator;
    }
}
