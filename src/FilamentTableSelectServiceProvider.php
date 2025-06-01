<?php

declare(strict_types=1);

namespace Dvarilek\FilamentTableSelect;

use Dvarilek\FilamentTableSelect\Components\Livewire\SelectionTable;
use Dvarilek\FilamentTableSelect\Testing\TestsTableSelect;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\View\TablesRenderHook;
use Filament\Support\Assets\Js;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentTableSelectServiceProvider extends PackageServiceProvider
{

    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-table-select')
            ->hasViews('filament-table-select')
            ->hasTranslations();
    }

    public function packageBooted(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views/', 'filament-table-select');

        Livewire::component('filament-table-select::selection-table-component', SelectionTable::class);

        FilamentView::registerRenderHook(
            TablesRenderHook::TOOLBAR_START,
            fn () => view('filament-table-select::selection-table-handler'),
            SelectionTable::class
        );

        FilamentAsset::register([
            Js::make('selection-modal-cache', __DIR__ . '/../resources/js/selection-modal-cache.js'),
        ], 'dvarilek/filament-table-select');

        Testable::mixin(new TestsTableSelect());
    }
}
