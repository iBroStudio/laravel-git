<?php

declare(strict_types=1);

use IBroStudio\Git\Dto\GitUserDto;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;

it('can return a Github user', function () {
    Saloon::fake([
        IBroStudio\Git\Integrations\Github\Requests\Users\GetGithubUserRequest::class => MockResponse::fixture('github/get_user'),
    ]);

    expect(
        githubConnector()
            ->user('Yann-iBroStudio')
            ->get()
    )->toBeInstanceOf(GitUserDto::class);
});
