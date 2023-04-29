<?php

namespace IBroStudio\Git\DtO;

use Carbon\Carbon;
use Spatie\LaravelData\Data;

class CommitData extends Data
{
    public function __construct(
        public string $hash,
        public string $title,
        public string $author,
        public string $email,
        public \DateTime $date,
    ) {
    }

    public static function fromString(string $string): self
    {
        [$hash, $title, $author, $email, $date] = explode(';', $string);

        return new self($hash, $title, $author, $email, Carbon::createFromTimestamp($date));
    }
}
