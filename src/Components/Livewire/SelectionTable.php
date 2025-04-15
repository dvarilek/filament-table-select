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

    /**
     * @var ?class-string<Model>
     */
    public ?string $relatedModel = null;

    /**
     * @var ?class-string<Resource>
     */
    public ?string $tableLocation = null;

    /**
     * @var ?Closure(HasTable $table): HasTable
     */
    protected ?Closure $configureSelectionTableUsing = null;

    /**
     * @param string $componentIdentifier
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function mount(string $componentIdentifier): void
    {
        $this->configureSelectionTableUsing = app()->make($componentIdentifier);
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

        $configureSelectionTableUsing = $this->configureSelectionTableUsing;

        if ($configureSelectionTableUsing !== null) {
            $table = $configureSelectionTableUsing($table);
        }

        return $table;
    }
}
