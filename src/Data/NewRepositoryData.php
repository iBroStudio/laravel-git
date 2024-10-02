<?php

namespace IBroStudio\Git\Data;

use IBroStudio\Git\Enums\GitProvidersEnum;
use IBroStudio\Git\Enums\GitRepositoryVisibilities;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;

class NewRepositoryData extends Data
{
    #[Computed]
    public string $path;

    public function __construct(
        public string $name,
        public string $localParentDirectory,
        #[WithCast(EnumCast::class)]
        public GitProvidersEnum $provider,
        #[WithCast(EnumCast::class)]
        public GitRepositoryVisibilities $visibility,
        public ?string $owner = null,
        public ?string $organization = null,
        public ?string $templateOwner = null,
        public ?string $templateRepo = null,
    ) {
        $this->path = Str::of($this->localParentDirectory)
            ->when(! Str::endsWith($this->localParentDirectory, '/'), function (Stringable $string) {
                return $string->append('/');
            })
            ->append($this->name);
    }
}
