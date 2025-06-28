# Laravel Git

A powerful Laravel package for managing Git repositories with an elegant, fluent API. This package provides a seamless integration between Laravel applications and Git repositories, allowing you to perform Git operations programmatically.

## Presentation and Goal

Laravel Git is designed to simplify Git operations in Laravel applications. It provides a clean, object-oriented interface to interact with Git repositories, making it easy to:

- Create, clone, and manage Git repositories
- Perform common Git operations (pull, push, fetch, commit)
- Work with branches, tags, and releases
- Integrate with GitHub (and potentially other Git providers in the future)
- Generate changelogs and manage versioning

The goal of this package is to provide a robust, Laravel-friendly way to interact with Git repositories, whether for deployment automation, version control management, or other Git-related tasks in your Laravel applications.

## Requirements

- PHP ^8.4
- Laravel 12
- Git

## Installation

You can install the package via Composer:

```bash
composer require ibrostudio/laravel-git
```

The package will automatically register its service provider.

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="IBroStudio\Git\GitServiceProvider"
```

This will create a `config/git.php` file where you can configure:

- Default Git provider, branch, and remote
- Authentication credentials for Git providers (GitHub)
- Repository templates
- Changelog configuration
- Pre-commit hooks
- Scripts for deployment, formatting, and testing

### GitHub Authentication

To use GitHub integration, set your GitHub credentials in your `.env` file:

```
GITHUB_USERNAME=your-github-username
GITHUB_TOKEN=your-github-personal-access-token
```

## Basic Usage

### Opening an Existing Repository

```php
use IBroStudio\Git\Repository;

// Open a repository from a local path
$repository = Repository::open('/path/to/repository');
```

### Cloning a Repository

```php
use IBroStudio\Git\Repository;

// Clone a repository
$repository = Repository::clone(
    url: 'git@github.com:username/repository.git',
    localParentDirectoryPath: '/path/to/parent/directory'
);
```

### Creating a New Repository

```php
use IBroStudio\Git\Repository;

$repository = Repository::init([
    'name' => 'new-repository',
    'localParentDirectory' => '/path/to/parent/directory',
]);
```
This will use all default values from git.php config file.

You can define more parameters and overwrite default values:

```php
use IBroStudio\Git\Repository;
use IBroStudio\DataObjects\Enums\GitProvidersEnum;
use IBroStudio\Git\Dto\OwnerDto\AuthOwnerDto;
use IBroStudio\Git\Dto\RepositoryDto\ConfigDto\RemoteDto;
use IBroStudio\DataObjects\ValueObjects\GitSshUrl;
use IBroStudio\Git\Enums\GitRepositoryVisibilitiesEnum;

$repository = Repository::init([
    'name' => 'new-repository',
    'branch' => 'main',
    'owner' => AuthOwnerDto::from(['name' => 'your-github-username']),
    'provider' => GitProvidersEnum::GITHUB,
    'remote' => RemoteDto::from([
        'name' => 'origin',
        'url' => GitSshUrl::build(GitProvidersEnum::GITHUB, 'your-github-username', 'new-repository'),
    ]),
    'localParentDirectory' => '/path/to/parent/directory',
    'visibility' => GitRepositoryVisibilitiesEnum::PRIVATE,
]);
```

### Basic Git Operations

```php
// Check repository status
$status = $repository->status();
$hasChanges = $repository->hasChanges();

// Fetch, pull, and push
$repository->fetch();
$repository->pull();
$repository->push();

// Restore changes
$repository->restore();
```

### Working with Commits

```php
// Get the last commit
$lastCommit = $repository->commits()->last();

// Get commit history
$history = $repository->commits()->history();

// Create a new commit
use IBroStudio\Git\Dto\RepositoryDto\CommitDto;

$commit = CommitDto::from([
    'message' => 'Your commit message',
    'description' => 'Optional longer description',
]);

$repository->commits()->add($commit);

// Undo the last commit
$repository->commits()->undo();
```

## Advanced Usage

### Working with Branches

```php
// Create a new branch
$repository->branches()->create('feature/new-feature');

// Switch to a branch
$repository->branches()->checkout('feature/new-feature');

// Merge branches
$repository->branches()->merge('feature/new-feature', 'main');
```

### Working with Tags and Releases

```php
// Create a tag
$repository->tags()->create('v1.0.0');

// Create a release
use IBroStudio\Git\Dto\RepositoryDto\ReleaseDto;

$release = ReleaseDto::from([
    'tag' => 'v1.0.0',
    'name' => 'Version 1.0.0',
    'description' => 'Release notes for version 1.0.0',
]);

$repository->releases()->create($release);
```

### Generating Changelogs

```php
$repository->changelog()->generate();
```

## API Documentation

The package provides several main classes and interfaces:

- `Git`: The main facade for interacting with Git providers
- `Repository`: Represents a Git repository with methods for common operations
- `Commit`: Represents a Git commit
- `Branch`: Represents a Git branch
- `Tag`: Represents a Git tag
- `Release`: Represents a Git release

Each class provides a fluent interface for performing operations related to its domain.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
