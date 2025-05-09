<?php

declare(strict_types=1);

use Dvarilek\FilamentTableSelect\Tests\src\Fixtures\TestLivewireComponent;
use Dvarilek\FilamentTableSelect\Tests\src\Models\Order;
use Filament\Forms\Components\Actions\Action;
use Dvarilek\FilamentTableSelect\Components\View\TableSelect;

use function Pest\Livewire\livewire;

beforeEach(function () {
    Order::factory()->create();
});

it('renders inside selection modal', function () {
    livewire(TestLivewireComponent::class, [
        'configureTableSelectComponentUsing' => fn (TableSelect $tableSelect) => $tableSelect
            ->requiresSelectionConfirmation()
            ->modifySelectionConfirmationAction(fn (Action $action) => $action->label('Custom Selection Confirmation Label'))
    ])
        ->assertFormComponentActionExists('products', 'selectionConfirmationAction')
        ->assertSelectionModalContains('Custom Selection Confirmation Label');
});

