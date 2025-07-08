<?php

declare(strict_types=1);

namespace IBroStudio\Git;

use IBroStudio\DataObjects\Enums\SemanticVersionEnum;
use IBroStudio\DataObjects\ValueObjects\DependenciesJsonFile;
use IBroStudio\Git\Dto\RepositoryDto\ReleaseDto;
use IBroStudio\Git\Exceptions\GitException;
use IBroStudio\Git\Processes\CreateReleaseProcess;
use IBroStudio\Tasks\Enums\ProcessStatesEnum;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use LogicException;

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
        try {

            return $this->repository->api()->releases()->latest();

        } catch (LogicException $exception) {

            return null;
        }
    }

    public function create(SemanticVersionEnum $versionType): ReleaseDto
    {
        $release = $this->latest() ?? ReleaseDto::from([
            'version' => DependenciesJsonFile::collectionFromPath($this->repository->path)
                ->first()
                ->version(prefix: config('git.default.version.prefix')),
        ]);

        /** @var CreateReleaseProcess $process */
        $process = $this->repository->process(CreateReleaseProcess::class, [
            'version' => $release->version->increment($versionType),
            'previous' => $release->version,
        ]);

        if ($process->state !== ProcessStatesEnum::COMPLETED) {
            throw new GitException('Failed to create release "'.$release->version->value.'"');
        }

        return $process->payload;
    }
}
