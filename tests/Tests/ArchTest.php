<?php

declare(strict_types=1);

arch('no debugging functions are used')
    ->expect(['dd', 'dump', 'die', 'var_dump', 'sleep', 'ray'])
    ->not->toBeUsed();
