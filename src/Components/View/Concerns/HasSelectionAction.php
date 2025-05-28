<?php

declare(strict_types=1);

namespace Dvarilek\FilamentTableSelect\Components\View\Concerns;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Field;
use Illuminate\Support\Js;
use Livewire\Component;
use Closure;

/**
 * @mixin Field
 */
trait HasSelectionAction
{

    /**
     * @var  null | Closure
     */
    protected ?Closure $modifySelectionActionUsing = null;

    /**
     * @return string
     */
    public function getSelectionActionName(): string
    {
        return 'tableSelectionAction';
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
        $action = Action::make($this->getSelectionActionName())
            ->label(trans_choice('filament-table-select::table-select.actions.selection.label', $this->getSelectionLimit()))
            ->modalContent($this->getSelectionTableView(...))
            ->mountUsing(function (Component $livewire, Field $component) {
                $statePath = Js::from($component->getStatePath());

                $livewire->js(<<<JS
                    Alpine.store('selectionModalCache').clear($statePath);
                JS);
            })
            ->disabled(fn (Select $component) => $component->isDisabled())
            ->modalSubmitAction(false)
            ->modalCancelAction(false)
            ->icon('heroicon-o-link')
            ->color('gray')
            ->slideOver();

        return $this->evaluate($this->modifySelectionActionUsing, [
            'action' => $action
        ], [
            Action::class => $action
        ]) ?? $action;
    }
}
