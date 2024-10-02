<?php

use IBroStudio\Git\Actions\TestCodeAction;
use IBroStudio\Git\GitRepository;

it('can run test code task', function () {
    (new TestCodeAction)
        ->execute(GitRepository::open(config('git.testing.repository')));
})->throwsNoExceptions();
