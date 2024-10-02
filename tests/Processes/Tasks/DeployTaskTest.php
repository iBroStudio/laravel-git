<?php

use IBroStudio\Git\Actions\DeployAction;
use IBroStudio\Git\Enums\EnvironmentTypes;
use IBroStudio\Git\GitRepository;
use IBroStudio\Git\Processes\Payloads\DeployPayload;

it('can run deploy task', function () {
    $payload = new DeployPayload(
        environmentType: EnvironmentTypes::TEST,
        repository: GitRepository::open(config('git.testing.repository'))
    );

    (new DeployAction)
        ->execute(
            environmentTypes: $payload->getEnvironmentType(),
            repository: $payload->getRepository()
        );
})->throwsNoExceptions();
