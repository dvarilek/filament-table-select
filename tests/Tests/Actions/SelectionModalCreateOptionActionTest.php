<?php

declare(strict_types=1);

use Dvarilek\FilamentTableSelect\Components\View\TableSelect;
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
            ->hasCreateOptionActionInSelectionModal()
            ->modifySelectionModalCreateOptionAction(fn (Action $action) => $action->label('Custom Selection Modal Create Option Action Label'))
    ])
        ->assertFormComponentActionExists('products', 'selectionModalCreateOptionAction')
        ->assertFormComponentActionHidden('products', 'createOption')
        ->assertSelectionModalContains('Custom Selection Modal Create Option Action Label');
});

it('can render create option action in form and in selection modal', function () {
    livewire(TestLivewireComponent::class, [
        'configureTableSelectComponentUsing' => fn (TableSelect $tableSelect) => $tableSelect
            ->createOptionForm(ProductResource::form(...))
            ->hasCreateOptionActionInSelectionModal()
            ->createOptionActionOnlyVisibleInSelectionModal(false)
    ])
        ->assertFormComponentActionExists('products', 'selectionModalCreateOptionAction')
        ->assertFormComponentActionVisible('products', 'createOption');
});

it('selection modal create option action is independent of create option action in form', function () {
    $livewire = livewire(TestLivewireComponent::class, [
        'configureTableSelectComponentUsing' => fn (TableSelect $tableSelect) => $tableSelect
            ->createOptionForm(ProductResource::form(...))
            ->hasCreateOptionActionInSelectionModal(createOptionActionOnlyVisibleInSelectionModal: false)
            ->createOptionAction(fn (Action $action) => $action->label('Custom Create Option Action Label'))
            ->modifySelectionModalCreateOptionAction(fn (Action $action) => $action->label('Custom Selection Modal Create Option Action Label'))
    ]);

    /* @var array<string, Action> $actions */
    $actions = $livewire->instance()->getForm('form')->getComponent('data.products')->getActions();

    expect($actions['createOption'])
        ->toBeInstanceOf(Action::class)
        ->getLabel()->toBe('Custom Create Option Action Label')
        ->and($actions['selectionModalCreateOptionAction'])
        ->toBeInstanceOf(Action::class)
        ->getLabel()->toBe('Custom Selection Modal Create Option Action Label');
});


