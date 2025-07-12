<?php

declare(strict_types=1);

namespace IBroStudio\Git\Contracts;

use IBroStudio\Git\Repository;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

interface RepositoryResourceContract
{
    public function all(): Collection|LazyCollection;

    public function byTopics(array $topics): Collection|LazyCollection;

    public function get(): Repository;

    public function create(): Repository;

    public function update();

    public function delete();

    public function tags();

    public function releases(): ReleaseResourceContract;
}
