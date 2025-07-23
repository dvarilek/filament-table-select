<?php

declare(strict_types=1);

namespace Dvarilek\FilamentTableSelect\Exceptions;

use Dvarilek\FilamentTableSelect\Components\Livewire\SelectionTable;
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
        $state = implode(',', $state);

        return new self("Selection table component cannot accept multiple records [{$state}] when only single selection is allowed.");
    }

    public static function incorrectSelectionTableLivewireComponent(): self
    {
        $component = SelectionTable::class;

        return new self("Selection table component must be sublcass of [{$component}]");
    }
}
