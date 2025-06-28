<?php

declare(strict_types=1);

namespace IBroStudio\Git\Processes\Tasks;

use Exception;
use IBroStudio\Git\Repository;
use IBroStudio\Tasks\Concerns\HasProcessableDto;
use IBroStudio\Tasks\Contracts\PayloadContract;
use IBroStudio\Tasks\Exceptions\AbortTaskAndProcessException;
use IBroStudio\Tasks\Models\Task;
use Parental\HasParent;

/**
 * @property Repository $processable_dto
 */
class FetchTask extends Task
{
    use HasParent;
    use HasProcessableDto;

    public function execute(PayloadContract $payload): PayloadContract|array
    {
        try {
            $this->processable_dto->fetch();
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
