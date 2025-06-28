<?php

declare(strict_types=1);

namespace IBroStudio\Git;

use IBroStudio\Git\Dto\RepositoryDto\CommitDto;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

class Commit
{
    public function __construct(protected Repository $repository) {}

    public function add(CommitDto $commitData): CommitDto
    {
        Process::path($this->repository->path)
            ->run("git add . && git commit -m '{$commitData->format()}'")
            ->throw();

        return $this->last();
    }

    public function last(): CommitDto
    {
        return $this->history(from: '-1', types_filtered: false)->first();
    }

    public function undo(): bool
    {
        return is_null(
            Process::path($this->repository->path)
                ->run('git reset --soft HEAD~')
                ->throw()
        );
    }

    public function history(?string $from = null, ?string $to = null, $types_filtered = true): LazyCollection
    {
        $command = Str::of("cd {$this->repository->path} && git log ")
            ->when(! is_null($from), function (Stringable $string) use ($from) {
                return $string->append("'{$from}'");
            })
            ->when(! is_null($from) && ! is_null($to), function (Stringable $string) use ($to) {
                return $string->append('..')->append("'{$to}' ");
            })
            ->append(' --date=iso8601-strict --pretty=medium')
            ->append(' -- '.$this->repository->path)
            ->toString();

        $history = LazyCollection::make(function () use ($command) {
            $handle = popen($command, 'r');
            while (($line = fgets($handle)) !== false) {
                yield $line;
            }
            pclose($handle);
        })
            ->map(function (string $line) {
                return Str::trim($line);
            })
            ->filter(function (string $line) {
                return ! empty($line);
            })
            ->chunk(4)
            ->map(function (LazyCollection $lines) {
                $values = array_values($lines->toArray());

                return CommitDto::from([
                    'hash' => Str::of($values[0])->after('commit')->trim()->toString(),
                    'author' => Str::of($values[1])->after('Author:')->trim()->toString(),
                    'date' => Str::of($values[2])->after('Date:')->trim()->toString(),
                    'message' => Str::of($values[3])->trim()->toString(),
                ]);
            })
            ->filter(function (CommitDto $commitData) use ($types_filtered) {
                return ! $types_filtered || in_array($commitData->type, config('git.changelog.included_types'));
            });

        return $history;
    }
}
