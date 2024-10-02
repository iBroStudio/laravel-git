<?php

namespace IBroStudio\Git;

use IBroStudio\Git\Contracts\GitProviderContract;
use IBroStudio\Git\Data\GitUserInfosData;
use IBroStudio\Git\Enums\GitProvidersEnum;
use Illuminate\Support\Collection;

class GitUser
{
    public static function infos(?GitProviderContract $provider = null): GitUserInfosData
    {
        return (
            $provider ?? app(GitProviderContract::class)->get(GitProvidersEnum::GITHUB->value)
        )
            ->user()
            ->infos();
    }

    public static function organizations(?GitProviderContract $provider = null): Collection
    {
        return (
            $provider ?? app(GitProviderContract::class)->get(GitProvidersEnum::GITHUB->value)
        )
            ->user()
            ->organizations();
    }

    public static function repositories(?GitProviderContract $provider = null): Collection
    {
        return (
            $provider ?? app(GitProviderContract::class)->get(GitProvidersEnum::GITHUB->value)
        )
            ->user()
            ->repositories();
    }
}
