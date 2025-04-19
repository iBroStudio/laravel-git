<?php

namespace IBroStudio\Git\Data\RepositoryData;

use IBroStudio\DataRepository\ValueObjects\GitSshUrl;
use IBroStudio\Git\Contracts\GitProviderContract;
use IBroStudio\Git\Data\RepositoryData\ConfigData\BranchData;
use IBroStudio\Git\Data\RepositoryData\ConfigData\CoreData;
use IBroStudio\Git\Data\RepositoryData\ConfigData\RemoteData;
use IBroStudio\Git\Enums\GitProvidersEnum;
use IBroStudio\Git\Enums\GitRepositoryVisibilitiesEnum;
use IBroStudio\Git\Exceptions\GitRepositoryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;

class RepositoryConfigData extends Data
{
    public function __construct(
        public CoreData $core,
        public RemoteData $remote,
        public BranchData $branch,
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

        if (! count($properties)) {
            throw new GitRepositoryException('Cannot retrieve repository properties', $path);
        }

        return new self(
            core: CoreData::from($properties['core']),
            remote: RemoteData::from($properties['remote']),
            branch: BranchData::from($properties['branch']),
        );
    }
}
