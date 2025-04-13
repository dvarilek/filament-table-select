<?php

declare(strict_types=1);

namespace Dvarilek\FilamentTableSelect\Components\View;

use Dvarilek\FilamentTableSelect\Components\View\Concerns\InteractsWithSelectionTable;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;

class TableSelect extends Select
{
    use InteractsWithSelectionTable;

    protected function setUp(): void
    {
        parent::setUp();

        $this->initializeTableSelectionAction();
    }

    /**
     * @return void
     */
    protected function initializeTableSelectionAction(): void
    {
        $tableSelectAction = $this->getSelectionAction();

        $this->suffixActions([$tableSelectAction]);
    }

    protected function getSelectionAction(): Action
    {
        return Action::make('tableSelectionAction')
            ->hidden(fn (Select $component, string $operation) => $component->isDisabled() || in_array($operation, ['view', 'viewOption']))
            ->color('gray')
            ->modalContent(fn (StaticAction $action) => $this->getSelectionTableView())
            ->modalSubmitAction(false)
            ->modalCancelAction(false);
    }
}