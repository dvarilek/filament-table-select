<?php

declare(strict_types=1);

namespace Dvarilek\FilamentTableSelect\Components\View\Concerns;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Field;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Js;
use Livewire\Component;
use Closure;

/**
 * @mixin Field
 */
trait HasSelectionAction
{

    /**
     * @var string | Alignment | Closure
     */
    protected string | Alignment | Closure $selectionActionAlignment = Alignment::Start;

    /**
     * @var bool | Closure
     */
    protected bool | Closure $shouldTriggerSelectionActionOnInputClick = false;

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
     * @param  string | Alignment $alignment
     *
     * @return $this
     */
    public function selectionActionAlignment(string | ALignment $alignment): static
    {
        $this->selectionActionAlignment = $alignment;

        return $this;
    }

    /**
     * @param  bool | Closure $shouldTriggerSelectionActionOnInputClick
     *
     * @return $this
     */
    public function triggerSelectionActionOnInputClick(bool | Closure $shouldTriggerSelectionActionOnInputClick = true): static
    {
        $this->shouldTriggerSelectionActionOnInputClick = $shouldTriggerSelectionActionOnInputClick;

        return $this;
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
     * @return string | Alignment
     */
    public function getSelectionActionAlignment(): string | Alignment
    {
        return $this->evaluate($this->selectionActionAlignment);
    }

    /**
     * @return bool
     */
    public function shouldTriggerSelectionActionOnInputClick(): bool
    {
        return (bool) $this->evaluate($this->shouldTriggerSelectionActionOnInputClick);
    }

    /**
     * @return Action
     */
    protected function getSelectionAction(): Action
    {
        $action = Action::make($this->getSelectionActionName())
            ->label(static fn(Field $component) => trans_choice(
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

        return $this->evaluate($this->modifySelectionActionUsing, [
            'action' => $action
        ], [
            Action::class => $action
        ]) ?? $action;
    }
}
