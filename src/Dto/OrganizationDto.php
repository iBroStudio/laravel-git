<?php

declare(strict_types=1);

namespace IBroStudio\Git\Dto;

use IBroStudio\Git\Integrations\Github\GithubResponse;
use Illuminate\Support\Arr;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class OrganizationDto extends Data
{
    public function __construct(
        #[MapInputName('login')]
        public string $name,
    ) {}

    public static function fromGithub(GithubResponse $response): self
    {
        $data = $response->json();

        return new self(
            name: $data['login'],
        );
    }

    public static function collectFromGithub(GithubResponse $response): array
    {
        return Arr::map($response->json(), function (array $data) {
            return self::from([
                'name' => $data['login'],
            ]);
        });
    }
}
