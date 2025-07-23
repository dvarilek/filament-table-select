<?php

declare(strict_types=1);

use Dvarilek\FilamentTableSelect\Components\Livewire\SelectionTable;
use Dvarilek\FilamentTableSelect\Tests\Fixtures\ProductResource;
use Dvarilek\FilamentTableSelect\Tests\Models\Order;
use Dvarilek\FilamentTableSelect\Tests\Models\Product;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;

use function Pest\Livewire\livewire;

it('can render selection table with default configuration', function () {
    $firstRecord = Product::factory()->create();
    $secondRecord = Product::factory()->create();

    $livewire = livewire(SelectionTable::class, [
        'model' => Order::class,
        'relationshipName' => 'products',
    ])
        ->assertCanSeeTableRecords([
            $firstRecord,
            $secondRecord,
        ]);

    /* @var Table $table */
    $table = $livewire->instance()->getTable();

    expect($table)
        ->toBeInstanceOf(Table::class)
        ->shouldDeselectAllRecordsWhenFiltered()->toBeFalse()
        ->getQueryStringIdentifier()->toBe('products-selection-table')
        ->getRecordAction($firstRecord)->toBe('selectTableRecord');

    $table->checkIfRecordIsSelectableUsing(fn (Product $record) => $record->getKey() !== $firstRecord->getKey());

    expect($table)
        ->getRecordAction($firstRecord)->toBeNull();
});

it('can configure selection table using a closure', function () {
    $livewire = livewire(SelectionTable::class, [
        'model' => Order::class,
        'relationshipName' => 'products',
        'modifySelectionTableUsing' => fn (Table $table) => $table->heading('Custom Heading'),
    ]);

    /* @var Table $table */
    $table = $livewire->instance()->getTable();

    expect($table)
        ->toBeInstanceOf(Table::class)
        ->getHeading()->toBe('Custom Heading');
});

it('can use table from another resource as a template for selection table', function () {
    $livewire = livewire(SelectionTable::class, [
        'model' => Order::class,
        'relationshipName' => 'products',
        'tableLocation' => ProductResource::class,
    ]);

    /* @var Table $table */
    $table = $livewire->instance()->getTable();

    expect($table)
        ->toBeInstanceOf(Table::class)
        ->getHeading()->toBe('Products')
        ->getDescription()->toBe('Custom Description From Resource');
});

it('adds a bulk action for showing checkboxes when no other bulk actions are available', function () {
    $emptyBulkActionsLivewire = livewire(SelectionTable::class, [
        'model' => Order::class,
        'relationshipName' => 'products',
        'modifySelectionTableUsing' => fn (Table $table) => $table->bulkActions([]),
    ]);

    $hiddenBulkActionLivewire = livewire(SelectionTable::class, [
        'model' => Order::class,
        'relationshipName' => 'products',
        'modifySelectionTableUsing' => fn (Table $table) => $table->bulkActions([
            BulkAction::make('test bulk action')->hidden(),
        ]),
    ]);

    $visibleBulkActionLivewire = livewire(SelectionTable::class, [
        'model' => Order::class,
        'relationshipName' => 'products',
        'modifySelectionTableUsing' => fn (Table $table) => $table->bulkActions([
            BulkAction::make('test bulk action'),
        ]),
    ]);

    /* @var Table $emptyBulkActionsTable */
    $emptyBulkActionsTable = $emptyBulkActionsLivewire->instance()->getTable();
    $emptyBulkActions = $emptyBulkActionsTable->getFlatBulkActions();

    /* @var Table $emptyBulkActionsTable */
    $hiddenBulkActionTable = $hiddenBulkActionLivewire->instance()->getTable();
    $hiddenBulkActions = $hiddenBulkActionTable->getFlatBulkActions();

    /* @var Table $visibleBulkActionTable */
    $visibleBulkActionTable = $visibleBulkActionLivewire->instance()->getTable();
    $visibleBulkActions = $visibleBulkActionTable->getFlatBulkActions();

    expect($emptyBulkActions)
        ->toHaveCount(1)
        ->and($emptyBulkActions['products-selection-table'])
        ->getExtraAttributes()->toBe([
            'x-show' => 'false',
            'wire:target' => null,
            'x-on:click' => null,
        ])
        ->and($hiddenBulkActions)
        ->toHaveCount(2)
        ->and($hiddenBulkActions['products-selection-table'])
        ->getExtraAttributes()->toBe([
            'x-show' => 'false',
            'wire:target' => null,
            'x-on:click' => null,
        ])
        ->and($visibleBulkActions)
        ->toHaveCount(1)
        ->and($visibleBulkActions['products-selection-table'] ?? null)->toBeNull();
});
