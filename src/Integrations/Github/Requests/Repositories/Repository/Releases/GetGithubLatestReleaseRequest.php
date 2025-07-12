<?php

declare(strict_types=1);

namespace IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\Releases;

use IBroStudio\DataObjects\Enums\GitProvidersEnum;
use IBroStudio\DataObjects\ValueObjects\GitSshUrl;
use IBroStudio\Git\Dto\RepositoryDto\ReleaseDto;
use IBroStudio\Git\Exceptions\GitException;
use IBroStudio\Git\Repository;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetGithubLatestReleaseRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(public ?Repository $repository = null, public ?GitSshUrl $url = null)
    {
        if (is_null($this->repository) && is_null($this->url)) {
            throw new GitException('GetGithubLatestReleaseRequest require repository or url argument.');
        }

        if (! is_null($this->url) && $this->url->provider !== GitProvidersEnum::GITHUB) {
            throw new GitException($this->url->value.' is not a Github url.');
        }
    }

    public function resolveEndpoint(): string
    {
        if (! is_null($this->url)) {
            return "/repos/{$this->url->username}/{$this->url->repository}/releases/latest";
        }

        return "/repos/{$this->repository->owner->name}/{$this->repository->name}/releases/latest";
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        return ReleaseDto::from($response);
    }
}
