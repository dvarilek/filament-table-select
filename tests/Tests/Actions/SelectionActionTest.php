<?php

declare(strict_types=1);

use Dvarilek\FilamentTableSelect\Components\Form\TableSelect;
use Dvarilek\FilamentTableSelect\Tests\Fixtures\TestLivewireComponent;
use Dvarilek\FilamentTableSelect\Tests\Models\Order;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Support\Facades\Lang;

use function Pest\Livewire\livewire;

beforeEach(function () {
    Order::factory()->create();
});

test('table selection action exists', function () {
    Lang::addLines([
        'filament-table-select::table-select.actions.selection-confirmation.label' => 'Test Confirm Label',
        'filament-table-select::table-select.actions.selection-create-option.label' => 'Test Create Option Label',
    ], app()->getLocale());

    livewire(TestLivewireComponent::class)
        ->assertFormComponentActionExists('products', 'tableSelectionAction')
        ->assertFormComponentActionDoesNotExist('products', [
            'selectionConfirmationAction',
            'selectionModalCreateOptionAction',
        ])
        ->assertSelectionModalDoesNotContains([
            'Test Confirm Label',
            'Test Create Option Label',
        ]);
});

it('can modify selection action', function () {
    livewire(TestLivewireComponent::class, [
        'configureTableSelectComponentUsing' => fn (TableSelect $tableSelect) => $tableSelect
            ->selectionAction(
                fn (Action $action) => $action
                    ->label('Custom Label')
                    ->icon('heroicon-o-user')
            ),
    ])
        ->assertFormComponentActionHasLabel('products', 'tableSelectionAction', 'Custom Label')
        ->assertFormComponentActionHasIcon('products', 'tableSelectionAction', 'heroicon-o-user');
});
