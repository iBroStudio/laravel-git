<?php

declare(strict_types=1);

namespace IBroStudio\Git\Contracts;

use IBroStudio\Git\Dto\RepositoryDto\ReleaseDto;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

interface ReleaseResourceContract
{
    public function all(): Collection|LazyCollection;

    public function get(int $release_id): ReleaseDto;

    public function create(ReleaseDto $releaseDto): ReleaseDto;

    public function update();

    public function delete();

    public function getByTag(string $tag): ReleaseDto;

    public function latest(): ReleaseDto;
}
