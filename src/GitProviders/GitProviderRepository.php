<?php

namespace IBroStudio\Git\GitProviders;

use IBroStudio\Git\Contracts\GitProviderRepositoryContract;
use IBroStudio\Git\Data\RepositoryData;
use Spatie\LaravelData\Data;

abstract class GitProviderRepository implements GitProviderRepositoryContract
{
    public function __construct(
        public RepositoryData $properties
    ) {}

    public function properties(): RepositoryData
    {
        return $this->properties;
    }

    public function update(Data|array $data): GitProviderRepositoryContract
    {
        $this->properties = RepositoryData::from(
            array_merge(
                $this->properties->except('path', 'fullname')->toArray(),
                is_array($data) ? $data : $data->toArray()
            )
        );

        return $this;
    }
}
