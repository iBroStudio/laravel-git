<?php

declare(strict_types=1);

namespace IBroStudio\Git\Dto\RepositoryDto;

use Carbon\CarbonImmutable;
use IBroStudio\Git\Enums\CommitTypeEnum;
use Illuminate\Support\Str;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;

class CommitDto extends Data
{
    public function __construct(
        #[WithCast(EnumCast::class)]
        public CommitTypeEnum $type,
        public string $message,
        public ?string $hash = null,
        public ?string $author = null,
        public ?CarbonImmutable $date = null,
    ) {}

    public static function fromType(CommitTypeEnum|string $type, string $message): self
    {
        return new self(
            type: is_string($type)
                ? CommitTypeEnum::tryFrom($type) ?? CommitTypeEnum::UNLISTED
                : $type,
            message: $message
        );
    }

    public static function fromArray(array $data): self
    {
        $split = Str::of($data['message'])
            ->explode(': ');

        if ($split->count() < 2) {
            $type = CommitTypeEnum::UNLISTED;
            $message = $data['message'];
        } elseif (Str::endsWith($split[0], '!')) {
            $type = CommitTypeEnum::BREAKING_CHANGES;
            $message = $split[1];
        } else {
            $type = CommitTypeEnum::tryFrom($split[0]) ?? CommitTypeEnum::UNLISTED;
            $message = $split[1];
        }

        return new self(
            type: $type,
            message: $message,
            hash: $data['hash'],
            author: $data['author'],
            date: is_null($data['date']) ? CarbonImmutable::now() : CarbonImmutable::createFromFormat('Y-m-d\TH:i:sP', $data['date']),
        );
    }

    public function format(): string
    {
        return $this->type->value.': '.Str::lcfirst($this->message);
    }
}
