<?php

declare(strict_types=1);

namespace Dvarilek\FilamentTableSelect\Components\View;

use Filament\Forms\Components\Select;

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
            fn (TableSelect $component) => $component->getSelectionAction(),
            function (TableSelect $component) {
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
            fn (TableSelect $component) => $component->evaluate($component->requiresSelectionConfirmation) ? $component->getSelectionConfirmationAction() : null,
            fn (TableSelect $component) => $component->evaluate($component->hasCreateOptionActionInSelectionModal) ? $component->getSelectionModalCreateOptionAction() : null
        ]);

        $this->dehydrateStateUsing(function (TableSelect $component, array $state) {
            return $component->isMultiple() ? $state : $state[0];
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
     * @return void
     */
    public function updateTableSelectComponentState(): void
    {
        $livewire = $this->getLivewire();

        $livewire->dispatch('filament-table-select::table-select.updateTableSelectComponentState',
            livewireId: $livewire->getId(),
            statePath: $this->getStatePath(),
        );
    }

    /**
     * @param  list<int | string> $state
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
