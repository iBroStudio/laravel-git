<?php

namespace IBroStudio\Git\GitProviders\Github;

use Github\ResultPager;
use IBroStudio\Git\Contracts\GitProviderUserContract;
use IBroStudio\Git\Data\GitUserInfosData;
use IBroStudio\Git\Data\GitUserOrganizationData;
use IBroStudio\Git\Data\RepositoryPropertiesData;
use IBroStudio\Git\GitProviders\GitProviderUser;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class GithubUser extends GitProviderUser implements GitProviderUserContract
{
    public function infos(): GitUserInfosData
    {
        $userRequest = $this->provider->api()->me()->show();

        $organizations = $this->organizations();

        $infos = GitUserInfosData::from([
            'name' => Arr::get($userRequest, 'login'),
            'organizations' => $organizations,
        ]);

        return $infos;
    }

    public function organizations(): Collection
    {
        $request = $this->provider->api()->me()->organizations();

        return collect(
            GitUserOrganizationData::collect($request)
        );
    }

    public function repositories(): Collection
    {
        $paginator = new ResultPager($this->provider->api()->connection());

        $request = $paginator
            ->fetchAll(
                $this->provider->api()->me(),
                'repositories',
                ['all']
            );

        return collect($request)
            ->map(function (array $repository) {
                return RepositoryPropertiesData::from($repository);
            });
    }
}
