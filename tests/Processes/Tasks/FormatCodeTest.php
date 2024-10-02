<?php

use IBroStudio\Git\Actions\FormatCodeAction;
use IBroStudio\Git\GitRepository;

it('can run test code task', function () {
    (new FormatCodeAction)
        ->execute(GitRepository::open(config('git.testing.repository')));
})->throwsNoExceptions();
