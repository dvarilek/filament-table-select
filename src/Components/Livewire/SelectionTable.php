<?php

declare(strict_types=1);

namespace Dvarilek\FilamentTableSelect\Components\Livewire;

use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Closure;
use Illuminate\Database\Eloquent\Model;

class SelectionTable extends TableWidget
{

    /**
     * @var bool
     */
    public bool $shouldSelectRecordOnRowClick = true;

    /**
     * @var ?class-string<Model>
     */
    public ?string $relatedModel = null;

    /**
     * @var ?class-string<Resource>
     */
    public ?string $tableLocation = null;

    /**
     * @var ?Closure(Table $table): Table
     */
    public ?Closure $modifySelectionTableUsing = null;

    /**
     * @return void
     */
    public function hydrate(): void
    {
        $this->dispatch('filament-table-select::selection-table.refresh-checkboxes');
    }

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

        return $table
            ->deselectAllRecordsWhenFiltered(false)
            ->queryStringIdentifier(strtolower(class_basename($this->relatedModel)) . "-selection-table")
            ->when(
                $this->shouldSelectRecordOnRowClick,
                fn (Table $table) => $table->recordAction(fn(Model $record) => $table->isRecordSelectable($record) ? 'selectTableRecord' : null)
            )
            ->when(
                $this->modifySelectionTableUsing instanceof Closure,
                fn (Table $table) => ($this->modifySelectionTableUsing)($table, $this)
            );
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
