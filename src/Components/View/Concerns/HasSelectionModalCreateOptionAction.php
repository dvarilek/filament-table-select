<?php

declare(strict_types=1);

namespace Dvarilek\FilamentTableSelect\Components\View\Concerns;

use Dvarilek\FilamentTableSelect\Enums\SelectionModalActionPosition;
use Dvarilek\FilamentTableSelect\Components\View\TableSelect;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Field;
use Closure;

/**
 * @mixin Field
 */
trait HasSelectionModalCreateOptionAction
{

    /**
     * @var bool | Closure
     */
    protected bool $hasCreateOptionActionInSelectionModal = true;

    /**
     * @var bool | Closure
     */
    protected bool $createOptionActionOnlyVisibleInSelectionModal = true;

    /**
     * @var ?Closure
     */
    protected ?Closure $modifySelectionModalCreateOptionActionUsing = null;

    /**
     * @var SelectionModalActionPosition
     */
    protected SelectionModalActionPosition $selectionModalCreateOptionActionPosition = SelectionModalActionPosition::TOP_RIGHT;

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
     * @param  Closure $modifySelectionModalCreateOptionActionUsing
     *
     * @return $this
     */
    public function modifySelectionModalCreateOptionAction(Closure $modifySelectionModalCreateOptionActionUsing): static
    {
        $this->modifySelectionModalCreateOptionActionUsing = $modifySelectionModalCreateOptionActionUsing;

        return $this;
    }

    /**
     * @return string
     */
    public function getSelectionModalCreateOptionActionName(): string
    {
        return 'selectionModalCreateOptionAction';
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
            throw new Exception("Select field [{$component->getStatePath()}] must have a [createOptionUsing()] closure set.");
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
}
