<?php

namespace IBroStudio\Git\Data;

use Carbon\Carbon;
use DateTime;
use IBroStudio\Git\Enums\GitCommitTypes;
use Illuminate\Support\Str;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;

class GitCommitData extends Data
{
    public function __construct(
        #[WithCast(EnumCast::class)]
        public GitCommitTypes $type,
        public string $message,
        public ?string $hash = null,
        public ?string $author = null,
        public ?DateTime $date = null,
    ) {}

    public function format(): string
    {
        return $this->type->value.': '.Str::lcfirst($this->message);
    }

    public static function fromMultiple(GitCommitTypes|string $type, string $message): self
    {
        return new self(
            type: is_string($type)
                ? GitCommitTypes::tryFrom($type) ?? GitCommitTypes::UNLISTED
                : $type,
            message: $message
        );
    }

    public static function fromArray(array $data): self
    {
        $split = Str::of($data['message'])
            ->explode(': ');

        if ($split->count() < 2) {
            $type = GitCommitTypes::UNLISTED;
            $message = $data['message'];
        } elseif (Str::endsWith($split[0], '!')) {
            $type = GitCommitTypes::BREAKING_CHANGES;
            $message = $split[1];
        } else {
            $type = GitCommitTypes::tryFrom($split[0]) ?? GitCommitTypes::UNLISTED;
            $message = $split[1];
        }

        return new self(
            type: $type,
            message: $message,
            hash: $data['hash'],
            author: $data['author'],
            date: Carbon::createFromFormat('Y-m-d\TH:i:sP', $data['date']),
        );
    }
}
