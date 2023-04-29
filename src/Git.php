<?php

namespace IBroStudio\Git;

class Git
{
    public function open(string $path): Repository
    {
        return new Repository($path);
    }
}
