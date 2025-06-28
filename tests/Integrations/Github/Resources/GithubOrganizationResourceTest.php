<?php

declare(strict_types=1);

use IBroStudio\Git\Dto\OrganizationDto;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;

it('can return a Github organization', function () {
    Saloon::fake([
        IBroStudio\Git\Integrations\Github\Requests\Organizations\GetGithubOrganizationRequest::class => MockResponse::fixture('github/get_organization'),
    ]);

    expect(
        githubConnector()
            ->organizations('iBroStudio')
            ->get()
    )->toBeInstanceOf(OrganizationDto::class);
});
