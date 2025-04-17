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
    public bool $isRecordSelectableOnRowClick = true;

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
    public ?Closure $configureSelectionTableUsing = null;

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

        if ($this->isRecordSelectableOnRowClick) {
            $table->recordAction('selectTableRecord');
        }

        $configureSelectionTableUsing = $this->configureSelectionTableUsing;

        if ($configureSelectionTableUsing !== null) {
            $table = $configureSelectionTableUsing($table);
        }

        return $table;
    }

    /**
     * @param  int $record
     *
     * @return void
     */
    public function selectTableRecord(int $record): void
    {
        $this->dispatch('selectTableRecord', $record);
    }
}
