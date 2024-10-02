<?php

namespace IBroStudio\Git\GitProviders\Github;

use IBroStudio\Git\Contracts\GitProviderRepositoryContract;
use IBroStudio\Git\Contracts\GitReleaseContract;
use IBroStudio\Git\Data\GithubRepositoryData;
use IBroStudio\Git\Enums\GitRepositoryVisibilities;
use IBroStudio\Git\Exceptions\GitRepositoryMissingPropertyException;
use IBroStudio\Git\GitProviders\GitProviderRepository;

class GithubRepository extends GitProviderRepository implements GitProviderRepositoryContract
{
    public function init(): GitProviderRepositoryContract
    {
        if ($this->properties->templateOwner !== null) {
            return $this->initFromTemplate();
        }

        $request = $this->properties->provider->api()->repo()->create(
            ...array_merge(
                [
                    'name' => $this->properties->name,
                    'public' => $this->properties->visibility === GitRepositoryVisibilities::PUBLIC,
                ],
                $this->properties->owner
                    && $this->properties->owner !== $this->properties->provider->user()->infos()->name
                ? ['organization' => $this->properties->owner] : []
            )
        );

        $this->update(GithubRepositoryData::from($request));

        return $this;
    }

    public function initFromTemplate(): GitProviderRepositoryContract
    {
        if (empty($this->properties->templateOwner)) {
            throw new GitRepositoryMissingPropertyException('Template owner is missing');
        }

        if (empty($this->properties->templateRepo)) {
            throw new GitRepositoryMissingPropertyException('Template repository is missing');
        }

        $request = $this->properties->provider->api()->repo()->createFromTemplate(
            templateOwner: $this->properties->templateOwner,
            templateRepo: $this->properties->templateRepo,
            parameters: [
                'name' => $this->properties->name,
                'owner' => $this->properties->owner ?? $this->properties->provider->user()->infos()->name,
            ]
        );

        if ($this->properties->visibility === GitRepositoryVisibilities::PRIVATE) {
            return $this->visibility(GitRepositoryVisibilities::PRIVATE);
        }

        return $this->update(GithubRepositoryData::from($request));
    }

    public function release(): GitReleaseContract
    {
        return new GithubRelease($this);
    }

    public function visibility(GitRepositoryVisibilities $visibility): GitProviderRepositoryContract
    {
        $request = $this->properties->provider->api()->repo()
            ->update(
                username: $this->properties->owner,
                repository: $this->properties->name,
                values: ['private' => $visibility === GitRepositoryVisibilities::PRIVATE]
            );

        return $this->update(GithubRepositoryData::from($request));
    }

    public function get(): GitProviderRepositoryContract
    {
        $request = $this->properties->provider->api()->repo()->show(
            username: $this->properties->owner,
            repository: $this->properties->name,
        );

        return $this->update(GithubRepositoryData::from($request));
    }

    public function delete(): bool
    {
        $request = $this->properties->provider->api()->repo()->remove(
            username: $this->properties->owner,
            repository: $this->properties->name
        );

        return empty($request);
    }
}
