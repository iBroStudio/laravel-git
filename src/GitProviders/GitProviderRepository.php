<?php

namespace IBroStudio\Git\GitProviders;

use IBroStudio\Git\Contracts\GitProviderRepositoryContract;
use IBroStudio\Git\Data\RepositoryPropertiesData;
use Spatie\LaravelData\Data;

abstract class GitProviderRepository implements GitProviderRepositoryContract
{
    public function __construct(
        public RepositoryPropertiesData $properties
    ) {}

    public function properties(): RepositoryPropertiesData
    {
        return $this->properties;
    }

    public function update(Data|array $data): GitProviderRepositoryContract
    {
        $this->properties = RepositoryPropertiesData::from(
            array_merge(
                $this->properties->except('path', 'fullname')->toArray(),
                is_array($data) ? $data : $data->toArray()
            )
        );

        return $this;
    }
}
