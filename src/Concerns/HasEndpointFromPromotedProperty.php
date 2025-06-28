<?php

declare(strict_types=1);

namespace IBroStudio\Git\Concerns;

trait HasEndpointFromPromotedProperty
{
    public function resolveEndpoint(): string
    {
        return $this->endpoint;
    }
}
