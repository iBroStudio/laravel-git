<?php

namespace IBroStudio\Git\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GitException extends Exception
{
    public function report(): bool
    {
        return false;
    }

    public function render(Request $request): Response|bool
    {
        return false;
    }
}
