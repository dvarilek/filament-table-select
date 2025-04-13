<?php

declare(strict_types=1);

namespace Dvarilek\FilamentTableSelect;

use Dvarilek\FilamentTableSelect\Components\Livewire\SelectionTable;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\View\TablesRenderHook;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentTableSelectServiceProvider extends PackageServiceProvider
{

    /**
     * @param  Package $package
     *
     * @return void
     */
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-table-select')
            ->hasViews('filament-table-select')
            ->hasTranslations();
    }

    /**
     * @return void
     */
    public function packageBooted(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views/', 'filament-table-select');

        Livewire::component('filament-table-select::selection-table-component', SelectionTable::class);

        FilamentView::registerRenderHook(
            TablesRenderHook::TOOLBAR_START,
            fn () => view('filament-table-select::selection-table-watcher'),
            SelectionTable::class
        );
    }
}
