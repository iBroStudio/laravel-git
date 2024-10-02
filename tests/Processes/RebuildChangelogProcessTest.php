<?php

use IBroStudio\Git\Contracts\ChangelogContract;
use IBroStudio\Git\GitRepository;
use IBroStudio\Git\Processes\RebuildChangelogProcess;
use Illuminate\Support\Facades\File;

it('can process a CHANGELOG rebuild', function () {
    File::delete(config('git.testing.repository').'/CHANGELOG.md');
    $process = RebuildChangelogProcess::handleWith([
        GitRepository::open(config('git.testing.repository')),
    ]);
    $changelog = app(ChangelogContract::class)
        ->bind($process->getRepository());

    expect(
        File::get(config('git.testing.repository').'/CHANGELOG.md')
    )->toBe(
        implode("\n", $changelog->content->flatten()->toArray())
    );
});
