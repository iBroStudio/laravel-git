<?php

declare(strict_types=1);

use IBroStudio\Git\Integrations\Github\GithubResponse;
use IBroStudio\Git\Integrations\Github\Requests\Users\GetGithubUserRequest;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;

it('can return GithubResponse from requests', function () {
    Saloon::fake([
        GetGithubUserRequest::class => MockResponse::fixture('github/get_user'),
    ]);

    expect(
        githubConnector()->send(new GetGithubUserRequest('Yann-iBroStudio'))
    )->toBeInstanceOf(GithubResponse::class);
});
