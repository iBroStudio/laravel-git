<?php

declare(strict_types=1);

namespace IBroStudio\Git\Processes\Tasks;

use Exception;
use IBroStudio\Git\Repository;
use IBroStudio\Tasks\Contracts\PayloadContract;
use IBroStudio\Tasks\Exceptions\AbortTaskAndProcessException;
use IBroStudio\Tasks\Models\Task;
use Parental\HasParent;

class CloneRepositoryTask extends Task
{
    use HasParent;

    /**
     * @param  Repository  $payload
     */
    public function execute(PayloadContract $payload): PayloadContract|array
    {
        try {
            retry([3000, 5000, 5000, 10000, 20000], function () use ($payload) {
                return Repository::clone(
                    url: $payload->remote->url->value,
                    localParentDirectoryPath: $payload->localParentDirectory,
                );
            });
        } catch (Exception $e) {
            throw new AbortTaskAndProcessException($this, $e->getMessage());
        }

        return $payload;
    }
}
