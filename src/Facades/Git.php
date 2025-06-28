<?php

declare(strict_types=1);

namespace IBroStudio\Git\Facades;

use IBroStudio\DataObjects\ValueObjects\GitSshUrl;
use IBroStudio\Git\Contracts\AuthResourceContract;
use IBroStudio\Git\Contracts\OrganizationResourceContract;
use IBroStudio\Git\Contracts\RepositoryResourceContract;
use IBroStudio\Git\Contracts\UserResourceContract;
use Illuminate\Support\Facades\Facade;

/**
 * @method static AuthResourceContract auth()
 * @method static OrganizationResourceContract organizations(?string $organization_name = null)
 * @method static RepositoryResourceContract repository(GitSshUrl $git)
 * @method static UserResourceContract user(?string $user_name = null)
 *
 * @see \IBroStudio\Git\Git
 */
class Git extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'git';
    }
}
