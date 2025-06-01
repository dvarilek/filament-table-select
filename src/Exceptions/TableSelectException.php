<?php

declare(strict_types=1);

namespace Dvarilek\FilamentTableSelect\Exceptions;

use Exception;

final class TableSelectException extends Exception
{
    /**
     * @param  list<string> $state
     *
     * @return self
     */
    public static function stateCountSurpassesSelectionLimit(array $state): self
    {
        return new self(sprintf(
            "Selection table component cannot accept multiple records (%s) when only single selection is allowed.",
            implode(',', $state)
        ));
    }
}
