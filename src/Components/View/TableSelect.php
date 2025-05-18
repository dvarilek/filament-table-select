<?php

declare(strict_types=1);

namespace Dvarilek\FilamentTableSelect\Components\View;

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Illuminate\Support\Js;

class TableSelect extends Select
{
    use Concerns\HasSelectionModalCreateOptionAction;
    use Concerns\HasSelectionAction;
    use Concerns\HasSelectionTable;

    /**
     * @var view-string
     */
    protected string $selectionTableModalView = 'filament-table-select::selection-table-modal';

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->suffixActions([
            static fn (TableSelect $component) => $component->getSelectionAction(),
            static function (TableSelect $component) {
                $action = $component->getAction($component->getCreateOptionActionName());

                if (! $action) {
                    return null;
                }

                if ($action->isDisabled()) {
                    return $action;
                }

                if (! $component->evaluate($component->hasCreateOptionActionInSelectionModal)) {
                    return $action;
                }

                if (! $component->evaluate($component->createOptionActionOnlyVisibleInSelectionModal)) {
                    return $action;
                }

                return $action->hidden()->disabled();
            },
        ]);

        $this->registerActions([
            static fn (TableSelect $component) => $component->evaluate($component->requiresSelectionConfirmation) ? $component->getSelectionConfirmationAction() : null,
            static fn (TableSelect $component) => $component->evaluate($component->hasCreateOptionActionInSelectionModal) ? $component->getSelectionModalCreateOptionAction() : null
        ]);

        $this->dehydrateStateUsing(static function (TableSelect $component, mixed $state) {
            if (is_array($state) && count($state) === 1 && ! $component->isMultiple()) {
                return $state[0];
            }

            return $state;
        });
    }

    /**
     * @return int
     */
    protected function getSelectionLimit(): int
    {
        return $this->isMultiple() ? $this->getOptionsLimit() : 1;
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

    public static function overwriteSelectionCreateOptionAction(Action $action, array $arguments, TableSelect $component, array $data, ComponentContainer $form): void
    {
        if (!$component->getCreateOptionUsing()) {
            throw new \Exception("Select field [{$component->getStatePath()}] must have a [createOptionUsing()] closure set.");
        }

        // If the key is not strvalled, 'selectedRecords' won't be treated properly by Filament Table.
        $createdOptionKey = strval($component->evaluate($component->getCreateOptionUsing(), [
            'data' => $data,
            'form' => $form,
        ]));

        $state = $component->isMultiple()
            ? [
                ...$component->getState(),
                $createdOptionKey,
            ]
            : $createdOptionKey;

        $selectionLimit = $component->getSelectionLimit();

        if ($component->evaluate($component->requiresSelectionConfirmation)) {
            $statePath = Js::from($component->getStatePath());
            $createdOptionKey = Js::from($createdOptionKey);

            $component->getLivewire()->js(<<<JS
                if ({$selectionLimit} === 1) {
                    Alpine.store('selectionModalCache').set($statePath, [$createdOptionKey]);

                    return;
                }

                if (Alpine.store('selectionModalCache').get($statePath)?.length >= $selectionLimit) {
                    return;
                }

                Alpine.store('selectionModalCache').push($statePath, $createdOptionKey);
            JS);
        } elseif ($selectionLimit === 1 || $selectionLimit >= count($state)) {
            $jsonState = Js::from(is_array($state) ? $state : [$state]);
            $statePath = Js::from($component->getStatePath());

            // Setting the component's state normally doesn't work on single selection
            $component->getLivewire()->js(<<<JS
                Alpine.store('selectionModalCache').set($statePath, $jsonState);

                \$nextTick(() => \$wire.set($statePath, $jsonState));
            JS);
        }

        if (! ($arguments['another'] ?? false)) {
            return;
        }

        $action->callAfter();

        $form->fill();
        $action->halt();
    }
}
