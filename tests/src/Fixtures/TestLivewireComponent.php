<?php

declare(strict_types=1);

namespace Dvarilek\FilamentTableSelect\Tests\src\Fixtures;

use Dvarilek\FilamentTableSelect\Tests\src\Models\Order;
use Dvarilek\FilamentTableSelect\Components\View\TableSelect;
use Filament\Forms\Components\Actions\Action;
use Closure;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;

class TestLivewireComponent extends Component implements HasForms
{
    use InteractsWithForms;

    public $data;

    public ?Closure $configureTableSelectComponentUsing = null;

    public static function make(): static
    {
        return new static;
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        $tableSelect = TableSelect::make('products')
            ->relationship('products', 'name');

        if ($this->configureTableSelectComponentUsing) {
            $tableSelect = ($this->configureTableSelectComponentUsing)($tableSelect);
        }

        return $form
            ->model(Order::query()->first())
            ->statePath('data')
            ->schema([
                $tableSelect
            ]);
    }

    /**
     * @param  string $actionName
     *
     * @return ?Action
     */
    public function getFormComponentAction(string $actionName): ?Action
    {
        foreach ($this->getCachedForms() as $form) {
            $component = $form->getComponent('products');

            if ($component && ($action = $component->getAction($actionName))) {
                return $action;
            }
        }

        return null;
    }

    public function data($data): static
    {
        $this->data = $data;

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function render(): string
    {
        return <<<'HTML'
            <div>
                 @foreach ($this->getCachedForms() as $form)
                    {{ $form }}
                 @endforeach
            </div>
        HTML;
    }
}