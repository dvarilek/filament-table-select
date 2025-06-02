<?php

declare(strict_types=1);

use Dvarilek\FilamentTableSelect\Components\Form\TableSelect;
use Dvarilek\FilamentTableSelect\Tests\Fixtures\ProductResource;
use Dvarilek\FilamentTableSelect\Tests\Fixtures\TestLivewireComponent;
use Dvarilek\FilamentTableSelect\Tests\Models\Order;
use Filament\Forms\Components\Actions\Action;
use function Pest\Livewire\livewire;

beforeEach(function () {
    Order::factory()->create();
});

it('renders inside selection modal', function () {
    livewire(TestLivewireComponent::class, [
        'configureTableSelectComponentUsing' => fn (TableSelect $tableSelect) => $tableSelect
            ->createOptionForm(ProductResource::form(...))
            ->createOptionAction(fn (Action $action) => $action->label('Custom Selection Modal Create Option Action Label'))
    ])
        ->assertFormComponentActionExists('products', 'selectionModalCreateOptionAction')
        ->assertSelectionModalContains('Custom Selection Modal Create Option Action Label');
});


