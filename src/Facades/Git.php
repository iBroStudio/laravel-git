<?php

namespace IBroStudio\Git\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \IBroStudio\Git\Git
 */
class Git extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \IBroStudio\Git\Git::class;
    }
}
