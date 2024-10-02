<?php

namespace IBroStudio\Git\Processes\Payloads\Contracts;

use IBroStudio\Git\Enums\EnvironmentTypes;

interface EnvironmentPayload
{
    public function getEnvironmentType(): EnvironmentTypes;
}
