<?php

declare(strict_types=1);

namespace Dvarilek\FilamentTableSelect\Components\Livewire;

use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Locked;

class SelectionTable extends TableWidget
{

    /**
     * @var bool
     */
    #[Locked]
    public bool $shouldSelectRecordOnRowClick = true;

    /**
     * @var ?class-string<Model>
     */
    #[Locked]
    public ?string $relatedModel = null;

    /**
     * @var ?class-string<Resource>
     */
    #[Locked]
    public ?string $tableLocation = null;

    /**
     * @var ?Closure(Table $table): Table
     */
    #[Locked]
    public ?Closure $modifySelectionTableUsing = null;

    /**
     * @param  Table $table
     *
     * @return Table
     */
    public function table(Table $table): Table
    {
        $table->query(fn () => $this->relatedModel::query());
        $tableLocation = $this->tableLocation;

        if ($tableLocation !== null) {
            $table = $tableLocation::table($table)->heading($tableLocation::getNavigationLabel());
        }

        $tableIdentifier = strtolower(class_basename($this->relatedModel)) . "-selection-table";

        return $table
            ->deselectAllRecordsWhenFiltered(false)
            ->queryStringIdentifier($tableIdentifier)
            ->when(
                $this->shouldSelectRecordOnRowClick,
                fn (Table $table) => $table->recordAction(fn(Model $record) => $table->isRecordSelectable($record) ? 'selectTableRecord' : null)
            )
            ->when(
                $this->modifySelectionTableUsing instanceof Closure,
                fn (Table $table) => ($this->modifySelectionTableUsing)($table, $this)
            )
            ->when(
                empty(array_filter($table->getFlatBulkActions(), fn (BulkAction $action) => $action->isVisible())),
                // Ensure that checkboxes are visible even when there are no bulk actions
                fn (Table $table) => $table->pushBulkActions([
                    BulkAction::make($tableIdentifier)->extraAttributes([
                        'x-show' => false,
                        'wire:target' => null,
                        'x-on:click' => null
                    ])
                ])
            );
    }

    /**
     * @return void
     */
    public function hydrate(): void
    {
        $this->dispatch('filament-table-select::selection-table.refresh-checkboxes');
    }

    /**
     * @param  mixed $record
     *
     * @return void
     */
    public function selectTableRecord(mixed $record): void
    {
        $this->dispatch('filament-table-select::selection-table.select-table-record', $record);
    }
}
