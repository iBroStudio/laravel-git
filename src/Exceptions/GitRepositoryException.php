<?php

declare(strict_types=1);

namespace IBroStudio\Git\Exceptions;

use Exception;

class GitRepositoryException extends Exception
{
    public function __construct(string $message, string $repositoryPath)
    {
        $message = "REPOSITORY '{$repositoryPath}'\n".$message;
        parent::__construct($message);
    }
}
