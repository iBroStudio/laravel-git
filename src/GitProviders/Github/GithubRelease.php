<?php

namespace IBroStudio\Git\GitProviders\Github;

use Github\Exception\RuntimeException;
use IBroStudio\DataRepository\ValueObjects\SemanticVersion;
use IBroStudio\Git\Changelog;
use IBroStudio\Git\Contracts\GitReleaseContract;
use IBroStudio\Git\Data\GitReleaseData;
use IBroStudio\Git\GitProviders\GitProviderRelease;
use Illuminate\Support\Collection;

class GithubRelease extends GitProviderRelease implements GitReleaseContract
{
    public function all(): Collection
    {
        $releases = Collection::make();

        $request = $this->repository->properties()->provider->api()->repo()->releases()->all(
            username: $this->repository->properties()->owner,
            repository: $this->repository->properties()->name
        );

        if (count($request)) {

            collect($request)
                ->sliding(2)
                ->each(function (Collection $release) use (&$releases) {
                    $releases->push(
                        GitReleaseData::from(data: $release->first(), previous: $release->last()['tag_name'])
                    );
                });

            $releases->push(
                GitReleaseData::from(end($request)) // to include the first release
            );
        }

        return $releases;
    }

    public function latest(): SemanticVersion
    {
        try {
            $request = $this->repository->properties()->provider->api()->repo()->releases()->latest(
                username: $this->repository->properties()->owner,
                repository: $this->repository->properties()->name
            );
        } catch (RuntimeException $e) {
            return SemanticVersion::make('v0.0.0');
        }

        return SemanticVersion::make($request['tag_name']);
    }

    public function create(SemanticVersion $version, Changelog $changelog): SemanticVersion
    {
        $description = $changelog->pick($version);

        $request = $this->repository->properties()->provider->api()->repo()->releases()->create(
            username: $this->repository->properties()->owner,
            repository: $this->repository->properties()->name,
            params: [
                'tag_name' => $version->value(),
                'body' => $description !== null ?
                    implode("\n", $description->get('lines')->flatten()->toArray()) : '',
            ]
        );

        return SemanticVersion::make($request['tag_name']);
    }
}
