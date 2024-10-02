<?php

namespace IBroStudio\Git\Processes\Payloads\Contracts;

use IBroStudio\Git\Data\NewRepositoryData;

interface NewRepositoryPayload
{
    public function getNewRepositoryData(): ?NewRepositoryData;
}
