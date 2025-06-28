<?php

declare(strict_types=1);

namespace IBroStudio\Git\Enums;

enum EnvironmentTypes: string
{
    case TEST = 'test';
    case PRODUCTION = 'production';
}
