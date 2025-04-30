<?php

declare(strict_types=1);

namespace Dvarilek\FilamentTableSelect\Components\View;

use Dvarilek\FilamentTableSelect\Components\View\Concerns\InteractsWithSelectionTable;
use Dvarilek\FilamentTableSelect\Enums\SelectionModalActionPosition;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Closure;
use Illuminate\Support\Js;
use Livewire\Component;

class TableSelect extends Select
{
    use InteractsWithSelectionTable;

    /**
     * @var view-string
     */
    protected string $selectionTableModalView = 'filament-table-select::selection-table-modal';

    /**
     * @var ?Closure
     */
    protected ?Closure $modifySelectionActionUsing = null;

    /**
     * @var bool | Closure
     */
    protected bool $hasCreateOptionActionInSelectionModal = true;

    /**
     * @var bool | Closure
     */
    protected bool $createOptionActionOnlyVisibleInSelectionModal = true;

    /**
     * @var SelectionModalActionPosition
     */
    protected SelectionModalActionPosition $selectionModalCreateOptionActionPosition = SelectionModalActionPosition::TOP_RIGHT;

    /**
     * @var ?Closure
     */
    protected ?Closure $modifySelectionModalCreateOptionActionUsing = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->suffixActions([
            fn () => $this->getSelectionAction(),
            function () {
                $action = $this->getAction($this->getCreateOptionActionName());

                if (! $action) {
                    return null;
                }

                if ($action->isDisabled()) {
                    return $action;
                }

                if (! $this->evaluate($this->hasCreateOptionActionInSelectionModal)) {
                    return $action;
                }

                if (! $this->evaluate($this->createOptionActionOnlyVisibleInSelectionModal)) {
                    return $action;
                }

                return $action->hidden()->disabled();
            },
        ]);

        $this->registerActions([
            fn () => $this->evaluate($this->requiresSelectionConfirmation) ? $this->getSelectionConfirmationAction() : null,
            fn () => $this->evaluate($this->hasCreateOptionActionInSelectionModal) ? $this->getSelectionModalCreateOptionAction() : null,
        ]);
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
     * @param bool | Closure $hasCreateOptionActionInSelectionModal
     * @param null | Closure  $createOptionActionOnlyVisibleInSelectionModal
     * @param null | Closure | SelectionModalActionPosition $selectionModalCreateOptionActionPosition
     *
     * @return $this
     */
    public function hasCreateOptionActionInSelectionModal(
        bool | Closure $hasCreateOptionActionInSelectionModal,
        null | Closure $createOptionActionOnlyVisibleInSelectionModal = null,
        null | Closure | SelectionModalActionPosition $selectionModalCreateOptionActionPosition = null
    ): static
    {
        $this->hasCreateOptionActionInSelectionModal = $hasCreateOptionActionInSelectionModal;
        $this->createOptionActionOnlyVisibleInSelectionModal = $createOptionActionOnlyVisibleInSelectionModal ?? $this->createOptionActionOnlyVisibleInSelectionModal;
        $this->selectionModalCreateOptionActionPosition = $selectionModalCreateOptionActionPosition ?? $this->selectionModalCreateOptionActionPosition;

        return $this;
    }

    /**
     * @param  SelectionModalActionPosition $selectionModalCreateOptionActionPosition
     *
     * @return $this
     */
    public function selectionModalCreateOptionActionPosition(SelectionModalActionPosition $selectionModalCreateOptionActionPosition): static
    {
        $this->selectionModalCreateOptionActionPosition = $selectionModalCreateOptionActionPosition;

        return $this;
    }

    /**
     * @param  bool | Closure $createOptionActionOnlyVisibleInSelectionModal
     *
     * @return $this
     */
    public function createOptionActionOnlyVisibleInSelectionModal(bool | Closure $createOptionActionOnlyVisibleInSelectionModal = true): static
    {
        $this->createOptionActionOnlyVisibleInSelectionModal = $createOptionActionOnlyVisibleInSelectionModal;

        return $this;
    }

    /**
     * @param  Closure $modifySelectionModalCreateOptionActionUsing
     *
     * @return $this
     */
    public function selectionModalCreateOptionAction(Closure $modifySelectionModalCreateOptionActionUsing): static
    {
        $this->modifySelectionModalCreateOptionActionUsing = $modifySelectionModalCreateOptionActionUsing;

        return $this;
    }

    /**
     * @return string
     */
    public function getSelectionActionName(): string
    {
        return 'tableSelectionAction';
    }

    /**
     * @return string
     */
    public function getSelectionModalCreateOptionActionName(): string
    {
        return 'createModalOption';
    }

    /**
     * @return int
     */
    protected function getSelectionLimit(): int
    {
        return $this->isMultiple() ? $this->getOptionsLimit() : 1;
    }

    /**
     * @return Action
     */
    protected function getSelectionAction(): Action
    {
        $action = Action::make($this->getSelectionActionName())
            ->label(trans_choice('filament-table-select::table-select.actions.selection.label', $this->getSelectionLimit()))
            ->modalContent(fn () => $this->getSelectionTableView()->with([
                'createAction' => $this->getAction($this->getSelectionModalCreateOptionActionName()),
                'createActionPosition' => $this->evaluate($this->selectionModalCreateOptionActionPosition),
            ]))
            ->mountUsing(function (Component $livewire, TableSelect $component) {
                $statePath = Js::from($component->getStatePath());

                $livewire->js(<<<JS
                    Alpine.store('selectionModalCache').clear($statePath);
                JS);
            })
            ->disabled(fn (TableSelect $component) => $component->isDisabled())
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

    /**
     * @return ?Action
     */
    protected function getSelectionModalCreateOptionAction(): ?Action
    {
        $createOptionAction = $this->getAction($this->getCreateOptionActionName());

        if (! $createOptionAction) {
            return null;
        }

        $selectionCreateOptionAction = (clone $createOptionAction)
            ->name($this->getSelectionModalCreateOptionActionName())
            ->label(__('filament-table-select::table-select.actions.selection-create-option.label'))
            ->modalHeading(__('filament-table-select::table-select.actions.selection-create-option.label'))
            ->disabledForm(false)
            ->button();

        if ($this->evaluate($this->createOptionActionOnlyVisibleInSelectionModal)) {
            $createOptionAction->hidden()->disabled();
        }

        $selectionCreateOptionAction->action(static::overwriteSelectionCreateOptionAction(...));

        return $this->evaluate($this->modifySelectionModalCreateOptionActionUsing, [
            'action' => $selectionCreateOptionAction
        ], [
            Action::class => $selectionCreateOptionAction
        ]) ?? $selectionCreateOptionAction;
    }

    protected static function overwriteSelectionCreateOptionAction(Action $action, array $arguments, TableSelect $component, array $data, ComponentContainer $form): void
    {
        if (! $component->getCreateOptionUsing()) {
            throw new \Exception("Select field [{$component->getStatePath()}] must have a [createOptionUsing()] closure set.");
        }

        $createdOptionKey = $component->evaluate($component->getCreateOptionUsing(), [
            'data' => $data,
            'form' => $form,
        ]);

        $state = is_array($state = $component->getState()) ? $state : [$state];
        $selectionLimit = $component->getSelectionLimit();

        $newState = $component->isMultiple()
            ? [
                ...$state,
                $createdOptionKey,
            ]
            : $createdOptionKey;

        if ((count($state) < $selectionLimit || $selectionLimit === 1) && ! $component->evaluate($component->requiresSelectionConfirmation)) {
            $component->state($newState);
            $component->callAfterStateUpdated();

        }

        $component->updateTableSelectCacheState(is_array($newState) ? $newState : [$newState]);

        if (! ($arguments['another'] ?? false)) {
            return;
        }

        $action->callAfter();

        $form->fill();

        $action->halt();
    }

    /**
     * @param  list<int | string>
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function updateTableSelectCacheState(array $state): void
    {
        $livewire = $this->getLivewire();

        $livewire->dispatch('filament-table-select::table-select.updateTableSelectCacheState',
            statePath: $this->getStatePath(),
            records: array_map(strval(...), $state),
            limit: $this->getSelectionLimit(),
            livewireId: $livewire->getId()
        );
    }
}
