<?php

declare(strict_types=1);

namespace IBroStudio\Git\Processes\Tasks;

use IBroStudio\Git\Dto\OwnerDto\AuthOwnerDto;
use IBroStudio\Git\Dto\OwnerDto\OrganizationOwnerDto;
use IBroStudio\Git\Git;
use IBroStudio\Git\Repository;
use IBroStudio\Tasks\Contracts\PayloadContract;
use IBroStudio\Tasks\Models\Task;
use Parental\HasParent;

class CreateRemoteRepositoryTask extends Task
{
    use HasParent;

    /**
     * @param  Repository  $payload
     */
    public function execute(PayloadContract $payload): PayloadContract|array
    {
        $git = Git::use($payload->provider);

        $entity = match (true) {
            $payload->owner instanceof OrganizationOwnerDto => $git->organization($payload->owner->name),
            $payload->owner instanceof AuthOwnerDto => $git->auth(),
        };

        $entity->repositories($payload)->create();

        return $payload;
    }
}
