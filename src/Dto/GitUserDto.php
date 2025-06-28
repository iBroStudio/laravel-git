<?php

declare(strict_types=1);

namespace IBroStudio\Git\Dto;

use IBroStudio\Git\Integrations\Github\GithubResponse;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class GitUserDto extends Data
{
    public function __construct(
        #[MapInputName('login')]
        public string $name,
        // ** @var Collection<int, GitUserOrganizationData> */
        // public Collection $organizations
    ) {}

    public static function fromGithub(GithubResponse $response): self
    {
        $data = $response->json();

        return new self(
            name: $data['login'],
        );
    }
}
