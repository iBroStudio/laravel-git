<?php

namespace IBroStudio\Git\Processes\Payloads\Concerns;

use IBroStudio\Git\Enums\EnvironmentTypes;

trait EnvironmentPayloadMethods
{
    public function getEnvironmentType(): EnvironmentTypes
    {
        return $this->environmentType;
    }
}
