<?php

declare(strict_types=1);

use IBroStudio\Git\Enums\GitCommitTypes;

return [

    'default' => [
        'branch' => 'main',
        'remote' => 'origin',
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
            GitCommitTypes::BREAKING_CHANGES,
            GitCommitTypes::FEAT,
            GitCommitTypes::FIX,
            GitCommitTypes::UNLISTED,
        ],
    ],

    'scripts' => [
        'deploy' => [
            'production' => null,
            'test' => null,
        ],
        'format-code' => [], //['composer format', 'bun run format'],
        'test-code' => [], //['composer test'],
    ],
];
