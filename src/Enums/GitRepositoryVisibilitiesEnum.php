<?php

declare(strict_types=1);

namespace IBroStudio\Git\Enums;

enum GitRepositoryVisibilitiesEnum: string
{
    case PUBLIC = 'public';
    case PRIVATE = 'private';
}
