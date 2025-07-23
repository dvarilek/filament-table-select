<?php

declare(strict_types=1);

namespace Dvarilek\FilamentTableSelect\Components\Form\Concerns;

use Closure;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Js;
use Livewire\Component;

/**
 * @mixin Field
 */
trait HasSelectionAction
{
    protected string | Alignment | Closure $selectionActionAlignment = Alignment::Start;

    protected bool | Closure $shouldTriggerSelectionActionOnInputClick = false;

    protected ?Closure $modifySelectionActionUsing = null;

    public function selectionActionAlignment(string | Alignment | Closure $alignment): static
    {
        $this->selectionActionAlignment = $alignment;

        return $this;
    }

    public function triggerSelectionActionOnInputClick(bool | Closure $shouldTriggerSelectionActionOnInputClick = true): static
    {
        $this->shouldTriggerSelectionActionOnInputClick = $shouldTriggerSelectionActionOnInputClick;

        return $this;
    }

    public function selectionAction(
        ?Closure $modifySelectionActionUsing,
        string | Alignment | null $alignment = null,
        bool | Closure | null $shouldTriggerSelectionActionOnInputClick = null
    ): static {
        $this->modifySelectionActionUsing = $modifySelectionActionUsing;
        $this->selectionActionAlignment = $alignment ?? $this->selectionActionAlignment;
        $this->shouldTriggerSelectionActionOnInputClick = $shouldTriggerSelectionActionOnInputClick ?? $this->shouldTriggerSelectionActionOnInputClick;

        return $this;
    }

    public function getSelectionActionAlignment(): string | Alignment
    {
        return $this->evaluate($this->selectionActionAlignment);
    }

    public function shouldTriggerSelectionActionOnInputClick(): bool
    {
        return (bool) $this->evaluate($this->shouldTriggerSelectionActionOnInputClick);
    }

    protected function getSelectionAction(): Action
    {
        $action = Action::make($this->getSelectionActionName())
            ->label(static fn (Field $component) => trans_choice(
                $component->isDisabled()
                    ? 'filament-table-select::table-select.actions.selection.view-label'
                    : 'filament-table-select::table-select.actions.selection.edit-label',
                $component->getSelectionLimit()
            ))
            ->modalContent(fn () => $this->getSelectionModalView())
            ->mountUsing(function (Component $livewire, Field $component) {
                $statePath = Js::from($component->getStatePath());

                $livewire->js(<<<JS
                    Alpine.store('selectionModalCache').clear($statePath);
                JS);
            })
            ->modalSubmitAction(false)
            ->modalCancelAction(false)
            ->icon('heroicon-o-link')
            ->link()
            ->color('primary')
            ->slideOver();

        if ($this->modifySelectionActionUsing) {
            $action = $this->evaluate($this->modifySelectionActionUsing, [
                'action' => $action,
            ], [
                Action::class => $action,
            ]) ?? $action;
        }

        return $action;
    }

    public function getSelectionActionName(): string
    {
        return 'tableSelectionAction';
    }
}
