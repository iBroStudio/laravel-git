<?php

declare(strict_types=1);

namespace IBroStudio\Git\Processes\Tasks;

use Exception;
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
class AddReleaseInChangelogTask extends Task
{
    use HasParent;
    use HasProcessableDto;

    /**
     * @param  ReleaseDto  $payload
     */
    public function execute(PayloadContract $payload): PayloadContract|array
    {
        try {

            $changelog = $this->processable_dto->changelog();

            $changelog->prepend($payload);

            if ($this->processable_dto->hasChanges()) {

                return $payload->updateDto([
                    'commit' => CommitDto::from(
                        CommitTypeEnum::CHORE,
                        'update CHANGELOG'
                    ),
                    'description' => $changelog->describe($payload->version),
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
