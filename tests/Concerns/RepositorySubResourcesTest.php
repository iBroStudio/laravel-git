<?php

declare(strict_types=1);

use IBroStudio\Git\Changelog;
use IBroStudio\Git\Commit;
use IBroStudio\Git\Release;

it('can return repository commits resources', function () {
    expect($this->repository->commits())->toBeInstanceOf(Commit::class);
});

it('can return repository releases resources', function () {
    expect($this->repository->releases())->toBeInstanceOf(Release::class);
});

it('can return repository changelog resources', function () {
    expect($this->repository->changelog())->toBeInstanceOf(Changelog::class);
});
