<?php

declare(strict_types=1);

namespace IBroStudio\Git\Processes\Tasks;

use Exception;
use IBroStudio\DataObjects\ValueObjects\DependenciesJsonFile;
use IBroStudio\Git\Contracts\CommittableContract;
use IBroStudio\Git\Repository;
use IBroStudio\Tasks\Concerns\HasProcessableDto;
use IBroStudio\Tasks\Contracts\PayloadContract;
use IBroStudio\Tasks\Exceptions\AbortTaskAndProcessException;
use IBroStudio\Tasks\Models\Task;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Process;
use Parental\HasParent;

/**
 * @property Repository $processable_dto
 */
class PreCommitTask extends Task
{
    use HasParent;
    use HasProcessableDto;

    /**
     * @param  CommittableContract  $payload
     */
    public function execute(PayloadContract $payload): PayloadContract|array
    {
        try {

            if (! in_array(get_class($payload), config('git.pre-commit.exclude'))) {

                $keys = $this->processable_dto->config('pre-commit.scripts');
                $scripts = new Collection;

                DependenciesJsonFile::collectionFromPath($this->processable_dto->path)
                    ->each(function (DependenciesJsonFile $file) use ($keys, $scripts) {
                        collect($file->data('scripts'))
                            ->filter(function (string|array $script, string $key) use ($keys) {
                                return in_array($key, $keys);
                            })
                            ->each(function (string|array $script) use ($scripts) {
                                if (is_array($script)) {
                                    return $scripts->push(...$script);
                                }

                                return $scripts->push($script);
                            });
                    });

                $scripts->each(function (string|array $script) {
                    Process::path($this->processable_dto->path)
                        ->run($script)
                        ->throw();
                });
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
