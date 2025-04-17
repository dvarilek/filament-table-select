<?php

declare(strict_types=1);

namespace Dvarilek\FilamentTableSelect\Components\View;

use Dvarilek\FilamentTableSelect\Components\View\Concerns\InteractsWithSelectionTable;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Closure;

class TableSelect extends Select
{
    use InteractsWithSelectionTable;

    /**
     * @var ?Closure
     */
    protected ?Closure $modifySelectionActionUsing = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->suffixAction(fn () => $this->getSelectionAction());
    }

    /**
     * @param  Closure $modifySelectionActionUsing
     *
     * @return $this
     */
    public function modifySelectionAction(Closure $modifySelectionActionUsing): static
    {
        $this->modifySelectionActionUsing = $modifySelectionActionUsing;

        return $this;
    }

    /**
     * @return Action
     */
    protected function getSelectionAction(): Action
    {
        $action = Action::make('tableSelectionAction')
            ->disabled(fn (Select $component, string $operation) => $component->isDisabled() || in_array($operation, ['view', 'viewOption']))
            ->slideOver()
            ->icon('heroicon-o-link')
            ->color('gray');

        $action = $this->evaluate($this->modifySelectionActionUsing, [
            'action' => $action
        ], [
            Action::class => $action
        ]) ?? $action;

        return $action
            ->modalContent(fn () => $this->getSelectionTableView())
            ->modalSubmitAction(false)
            ->modalCancelAction(false);
    }

    /**
     * @return int
     */
    protected function getSelectionLimit(): int
    {
        return $this->isMultiple() ? $this->getOptionsLimit() : 1;
    }
}
