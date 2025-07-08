<?php

declare(strict_types=1);

namespace IBroStudio\Git\Processes\Tasks;

use Exception;
use IBroStudio\DataObjects\ValueObjects\DependenciesJsonFile;
use IBroStudio\Git\Dto\RepositoryDto\CommitDto;
use IBroStudio\Git\Dto\RepositoryDto\ReleaseDto;
use IBroStudio\Git\Enums\CommitTypeEnum;
use IBroStudio\Git\Repository;
use IBroStudio\Tasks\Concerns\HasProcessableDto;
use IBroStudio\Tasks\Contracts\PayloadContract;
use IBroStudio\Tasks\Exceptions\AbortTaskAndProcessException;
use IBroStudio\Tasks\Models\Task;
use Parental\HasParent;

/**
 * @property Repository $processable_dto
 */
class BumpVersionInDependenciesFilesTask extends Task
{
    use HasParent;
    use HasProcessableDto;

    /**
     * @param  ReleaseDto  $payload
     */
    public function execute(PayloadContract $payload): PayloadContract|array
    {
        try {
            DependenciesJsonFile::collectionFromPath($this->processable_dto->path)
                ->each(function (DependenciesJsonFile $file) use ($payload) {
                    $file->version($payload->version);
                });

            if ($this->processable_dto->hasChanges()) {

                return $payload->updateDto([
                    'commit' => CommitDto::from(
                        CommitTypeEnum::CHORE,
                        "bump version to {$payload->version->withoutPrefix()}"
                    ),
                ]);
            }

        } catch (Exception $e) {
            throw new AbortTaskAndProcessException($this, $e->getMessage());
        }

        return $payload;
    }

    protected function getProcessableDtoClass(): string
    {
        return Repository::class;
    }
}
