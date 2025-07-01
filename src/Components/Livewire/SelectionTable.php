<?php

declare(strict_types=1);

namespace Dvarilek\FilamentTableSelect\Components\Livewire;

use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Filament\Resources\Resource;
use Closure;
use Illuminate\Database\Eloquent\Relations\Relation;
use Filament\Support\Services\RelationshipJoiner;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Locked;

class SelectionTable extends TableWidget
{
    #[Locked]
    public bool $shouldSelectRecordOnRowClick = true;

    #[Locked]
    public bool $isDisabled = false;

    /**
     * @var  class-string<Model> | null
     */
    #[Locked]
    public ?string $model = null;

    /**
     * @var  null | Model
     */
    #[Locked]
    public ?Model $record = null;

    public string $relationshipName;

    /**
     * @var  class-string<mixed | Resource> | null
     */
    #[Locked]
    public ?string $tableLocation = null;

    /**
     * @var  null | Closure(Table $table, self $selectionTable): Table
     */
    #[Locked]
    public ?Closure $modifySelectionTableUsing = null;

    public function table(Table $table): Table
    {
        $tableLocation = $this->tableLocation;

        if ($tableLocation !== null) {
            $table = $tableLocation::table($table);

            if (is_subclass_of($tableLocation, Resource::class)) {
                $table->heading($tableLocation::getNavigationLabel());
            }
        }

        $table->query(function (): Builder {
            $relationship = Relation::noConstraints(fn (): Relation => ($this->record ??= app($this->model))->{$this->relationshipName}());

            $relationshipQuery = app(RelationshipJoiner::class)->prepareQueryForNoConstraints($relationship);

            if (! ($relationship instanceof BelongsToMany)) {
                return $relationshipQuery;
            }

            $relationshipBaseQuery = $relationshipQuery->getQuery();

            if (blank($relationshipBaseQuery->joins ?? [])) {
                return $relationshipQuery;
            }

            array_shift($relationshipBaseQuery->joins);

            return $relationshipQuery;
        });

        $tableIdentifier = $this->relationshipName . "-selection-table";

        return $table
            ->deselectAllRecordsWhenFiltered(false)
            ->queryStringIdentifier($tableIdentifier)
            ->when(
                $this->isDisabled,
                fn (Table $table) => $table->selectable(false)
            )
            ->when(
                !$this->isDisabled && $this->shouldSelectRecordOnRowClick,
                fn (Table $table) => $table->recordAction(fn(Model $record) => $table->isRecordSelectable($record) ? 'selectTableRecord' : null)
            )
            ->when(
                $this->modifySelectionTableUsing instanceof Closure,
                fn (Table $table) => ($this->modifySelectionTableUsing)($table, $this)
            )
            ->when(
                !$this->isDisabled && blank(array_filter($table->getFlatBulkActions(), fn (BulkAction $action) => $action->isVisible())),
                // Ensure that checkboxes are visible even when there are no bulk actions
                fn (Table $table) => $table->pushBulkActions([
                    BulkAction::make($tableIdentifier)->extraAttributes([
                        'x-show' => 'false',
                        'wire:target' => null,
                        'x-on:click' => null
                    ])
                ])
            );
    }

    public function hydrate(): void
    {
        $this->dispatch('filament-table-select::selection-table.refresh-checkboxes');
    }

    public function selectTableRecord(mixed $record): void
    {
        $this->dispatch('filament-table-select::selection-table.select-table-record', $record);
    }
}
