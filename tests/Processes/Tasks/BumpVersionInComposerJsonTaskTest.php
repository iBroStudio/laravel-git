<?php

use IBroStudio\DataRepository\Enums\SemanticVersionSegments;
use IBroStudio\DataRepository\ValueObjects\VersionedComposerJson;
use IBroStudio\Git\Actions\BumpVersionInComposerJsonAction;
use IBroStudio\Git\Data\GitReleaseData;
use IBroStudio\Git\GitRepository;
use IBroStudio\Git\Processes\Payloads\CreateRepositoryReleasePayload;
use Illuminate\Support\Facades\File;

it('can bump version in composer.json', function () {
    $repository = GitRepository::open('/Volumes/LaCie/dev/php-packages/test/test');
    $version = $repository->release()->latest();
    $newVersion = $version->increment(SemanticVersionSegments::PATCH);
    $payload = new CreateRepositoryReleasePayload(
        repository: $repository,
        releaseData: new GitReleaseData(
            version: $newVersion,
            previous: $version,
            published_at: new DateTime,
        )
    );

    $bump = (new BumpVersionInComposerJsonAction)->execute(
        composerJson: VersionedComposerJson::make(
            $payload->getRepository()->properties->path.'/composer.json'
        ),
        version: $payload->getReleaseData()->version
    );

    expect($bump)->toBeTrue();

    $composer = File::json($payload->getRepository()->properties->path.'/composer.json');

    expect(
        $composer['version']
    )->toEqual($newVersion->withoutPrefix()->value());

    $repository->restore();
});
