<?php

namespace IBroStudio\Git\Data;

use DateTime;
use IBroStudio\DataRepository\ValueObjects\SemanticVersion;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class GitReleaseData extends Data
{
    #[Computed]
    public array $gitLogBoundaries;

    public function __construct(
        #[MapInputName('tag_name')]
        public SemanticVersion|string $version,
        public SemanticVersion|string|null $previous = null,
        public ?DateTime $published_at = null,
    ) {
        if (is_string($this->version)) {
            $this->version = SemanticVersion::make($this->version);
        }

        if (is_string($this->previous)) {
            $this->previous = SemanticVersion::make($this->previous);
        }

        if (
            ! is_null($this->previous)
            && $this->previous->withoutPrefix()->value() !== '0.0.0'
            && is_null($this->published_at)
        ) {
            $this->gitLogBoundaries = ['from' => $this->previous->value(), 'to' => 'HEAD'];
        }

        if (! is_null($this->previous) && ! is_null($this->published_at)) {
            $this->gitLogBoundaries = ['from' => $this->previous->value(), 'to' => $this->version->value()];
        }

        if (is_null($this->published_at)) {
            $this->published_at = new DateTime;
        }
    }

    public static function fromMultiple(array $data, string $previous): self
    {
        return self::from([...$data, 'previous' => $previous]);
    }

    public static function fromReleases(SemanticVersion $newVersion, SemanticVersion $currentVersion): self
    {
        return new self(
            version: $newVersion,
            previous: $currentVersion
        );
    }
}
