<?php

declare(strict_types=1);

// @phpstan-ignore-next-line
arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();
