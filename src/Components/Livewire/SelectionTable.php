<?php

declare(strict_types=1);

namespace Dvarilek\FilamentTableSelect\Components\Livewire;

use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Closure;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;

class SelectionTable extends TableWidget
{

    // TODO: move isMultiple and selectionLimit properties to the js side

    /**
     * @var bool
     */
    public bool $isMultiple = false;

    /**
     * @var int
     */
    public int $selectionLimit = 0;

    /**
     * @var ?class-string<Model>
     */
    public ?string $relatedModel = null;

    /**
     * @var ?class-string<Resource>
     */
    public ?string $tableLocation = null;

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

        return $table;
    }
}