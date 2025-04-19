<?php

namespace IBroStudio\Git\Integrations\Github\Resources;

use IBroStudio\Git\Integrations\Github\Requests\Users\GetGithubUser;
use Saloon\Http\BaseResource;

class GithubUser extends BaseResource
{
    public function get(string $username)
    {
        dd(
            $this->connector->send(
                new GetGithubUser($username)
            )->array()
        );
        return $this->connector->send(
            new GetGithubUser($username)
        )->dtoOrFail();
    }
}
