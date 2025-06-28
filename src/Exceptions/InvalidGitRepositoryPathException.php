<?php

declare(strict_types=1);

namespace IBroStudio\Git\Exceptions;

use Exception;

class InvalidGitRepositoryPathException extends Exception
{
    public function __construct(string $path)
    {
        parent::__construct("\"{$path}\" is not a valid path.");
    }
}
