<?php

declare(strict_types=1);

namespace IBroStudio\Git;

use IBroStudio\DataObjects\Enums\SemanticVersionEnum;
use IBroStudio\Git\Dto\RepositoryDto\ReleaseDto;
use IBroStudio\Git\Processes\CreateReleaseProcess;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

class Release
{
    public function __construct(protected Repository $repository) {}

    /**
     * @return Collection<int, ReleaseDto>|LazyCollection<int, ReleaseDto>
     */
    public function all(): Collection|LazyCollection
    {
        return $this->repository->api()->releases()->all();
    }

    public function latest(): ?ReleaseDto
    {
        return $this->repository->api()->releases()->latest();
    }

    public function create(SemanticVersionEnum $versionType): ReleaseDto
    {
        $release = $this->latest();

        /** @var CreateReleaseProcess $process */
        $process = $this->repository->process(CreateReleaseProcess::class, [
            'version' => $release->version->increment($versionType),
            'previous' => $release->version,
        ]);

        return $process->payload;
    }
}
