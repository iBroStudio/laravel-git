<?php

declare(strict_types=1);

namespace IBroStudio\Git\Dto\RepositoryDto;

use IBroStudio\Git\Dto\RepositoryDto\ConfigDto\BranchDto;
use IBroStudio\Git\Dto\RepositoryDto\ConfigDto\CoreDto;
use IBroStudio\Git\Dto\RepositoryDto\ConfigDto\RemoteDto;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use Spatie\LaravelData\Data;

class RepositoryConfigDto extends Data
{
    public function __construct(
        public CoreDto $core,
        public RemoteDto $remote,
        public BranchDto $branch,
    ) {}

    public static function fromPath(string $path): self
    {
        $config = Process::path($path)
            ->run('git config --local -l')
            ->throw();

        $properties = [];

        Str::of($config->output())
            ->split('/\n/')
            ->mapWithKeys(function (string $line) use (&$properties) {
                $split = Str::of($line)->split('/=/');

                return data_set($properties, $split->first(), $split->last(), overwrite: false);
            });

        $properties = RepositoryLocalPropertiesDto::from($properties);

        return new self(
            core: CoreDto::from($properties),
            remote: RemoteDto::from($properties),
            branch: BranchDto::from($properties),
        );
    }
}
