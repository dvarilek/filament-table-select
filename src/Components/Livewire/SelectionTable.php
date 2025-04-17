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

        $configureSelectionTableUsing = $this->configureSelectionTableUsing;

        if ($configureSelectionTableUsing !== null) {
            $table = $configureSelectionTableUsing($table);
        }

        return $table;
    }
}
