<?php

declare(strict_types=1);

use Dvarilek\FilamentTableSelect\Components\Livewire\SelectionTable;
use Dvarilek\FilamentTableSelect\Tests\Fixtures\ProductResource;
use Dvarilek\FilamentTableSelect\Tests\Models\Product;
use Filament\Tables\Table;
use function Pest\Livewire\livewire;

it('can render selection table with default configuration', function () {
    $firstRecord = Product::factory()->create();
    $secondRecord = Product::factory()->create();

    $livewire = livewire(SelectionTable::class, [
        'relatedModel' => Product::class,
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
        ->getQueryStringIdentifier()->toBe("product-selection-table")
        ->getRecordAction($firstRecord)->toBe('selectTableRecord');

    $table->checkIfRecordIsSelectableUsing(fn (Product $record) => $record->getKey() !== $firstRecord->getKey());

    expect($table)
        ->getRecordAction($firstRecord)->toBeNull();
});

it('can configure selection table using a closure', function () {
    $livewire = livewire(SelectionTable::class, [
        'relatedModel' => Product::class,
        'modifySelectionTableUsing' => fn (Table $table) => $table->heading('Custom Heading')
     ]);

    /* @var Table $table */
    $table = $livewire->instance()->getTable();

    expect($table)
        ->toBeInstanceOf(Table::class)
        ->getHeading()->toBe('Custom Heading');
});

it('can use table from another resource as a template for selection table', function () {
    $livewire = livewire(SelectionTable::class, [
        'relatedModel' => Product::class,
        'tableLocation' => ProductResource::class,
    ]);

    /* @var Table $table */
    $table = $livewire->instance()->getTable();

    expect($table)
        ->toBeInstanceOf(Table::class)
        ->getHeading()->toBe('Products')
        ->getDescription()->toBe('Custom Description From Resource');
});