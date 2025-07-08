<?php

declare(strict_types=1);

use IBroStudio\Git\Dto\OwnerDto\AuthOwnerDto;
use IBroStudio\Git\Dto\OwnerDto\OrganizationOwnerDto;
use IBroStudio\Git\Dto\RepositoryDto\ReleaseDto;
use IBroStudio\Git\Enums\CommitTypeEnum;

return [

    'default' => [
        'provider' => IBroStudio\DataObjects\Enums\GitProvidersEnum::GITHUB,
        'branch' => 'main',
        'remote' => 'origin',
        'owner' => [
            'name' => env('GITHUB_USERNAME'), // env('GITHUB_ORGANIZATION')
            'type' => AuthOwnerDto::class, // OrganizationOwnerDto::class
        ],
        'version' => [
            'prefix' => 'v.',
        ],
    ],

    'auth' => [
        'github' => [
            'username' => env('GITHUB_USERNAME'),
            'token' => env('GITHUB_TOKEN'),
        ],
    ],

    'templates' => [
        'git@github.com:spatie/package-skeleton-laravel.git',
        'git@github.com:filamentphp/plugin-skeleton.git',
    ],

    'changelog' => [
        'file' => 'CHANGELOG.md',
        'header' => [
            '# Changelog',
            'All notable changes to this project will be documented in this file.',
            ' ',
        ],
        'version_header_tag' => '##',
        'type_header_tag' => '###',
        'section_delimiter' => '---',
        'included_types' => [
            CommitTypeEnum::BREAKING_CHANGES,
            CommitTypeEnum::FEAT,
            CommitTypeEnum::FIX,
            CommitTypeEnum::UNLISTED,
        ],
    ],

    'pre-commit' => [
        'scripts' => ['analyse', 'format', 'test'],
        'exclude' => [ReleaseDto::class],
    ],

    'scripts' => [
        'deploy' => [
            'production' => null,
            'test' => null,
        ],
        'format-code' => [], // ['composer format', 'bun run format'],
        'test-code' => [], // ['composer test'],
    ],

    'log_git_processes' => false,
];
