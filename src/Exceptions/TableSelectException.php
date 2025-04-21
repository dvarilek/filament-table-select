<?php

declare(strict_types=1);

namespace Dvarilek\FilamentTableSelect\Exceptions;

use Exception;

final class TableSelectException extends Exception
{

    /**
     * @return self
     */
    public static function createOptionActionNotFound(): self
    {
        return new self("Component must define a create option action to support create option action in selection modal");
    }
}