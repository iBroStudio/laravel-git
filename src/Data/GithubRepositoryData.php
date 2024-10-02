<?php

namespace IBroStudio\Git\Data;

use IBroStudio\DataRepository\ValueObjects\GitSshUrl;
use IBroStudio\Git\Contracts\GitProviderRepositoryDataContract;
use IBroStudio\Git\Enums\GitProvidersEnum;
use IBroStudio\Git\Enums\GitRepositoryVisibilities;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class GithubRepositoryData extends Data implements GitProviderRepositoryDataContract
{
    #[Computed]
    public ?string $templateOwner = null;

    #[Computed]
    public ?string $templateRepo = null;

    public function __construct(
        public string $name,
        public string|array $owner,
        public GitSshUrl|string $ssh_url,
        #[MapInputName('default_branch')]
        public string $branch,
        public GitRepositoryVisibilities|string $visibility,
        public string $html_url,
        public GitProvidersEnum $provider = GitProvidersEnum::GITHUB,
        public array $template_repository = [],
        //public string $remote = 'origin',
    ) {
        if (is_array($this->owner)) {
            $this->owner = $owner['login'];
        }

        if (is_string($this->ssh_url)) {
            $this->ssh_url = GitSshUrl::make($this->ssh_url);
        }

        if (is_string($this->visibility)) {
            $this->visibility = GitRepositoryVisibilities::from($this->visibility);
        }

        if (count($this->template_repository)) {
            $this->templateOwner = $this->template_repository['owner']['login'];
            $this->templateRepo = $this->template_repository['name'];
        }
    }
}
