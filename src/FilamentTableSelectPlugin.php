<?php

declare(strict_types=1);

namespace Dvarilek\FilamentTableSelect;

use Dvarilek\FilamentTableSelect\Components\Livewire\SelectionTable;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Tables\View\TablesRenderHook;

class FilamentTableSelectPlugin implements Plugin
{

    /**
     * @return string
     */
    public function getId(): string
    {
        return 'filament-table-select';
    }

    /**
     * @return static
     */
    public static function make(): static
    {
        return app(static::class);
    }

    /**
     * @param  Panel $panel
     *
     * @return void
     */
    public function register(Panel $panel): void
    {

    }

    /**
     * @param  Panel $panel
     *
     * @return void
     */
    public function boot(Panel $panel): void
    {
        // ...
    }

    /**
     * @return static
     */
    public static function get(): static
    {
        /* @var static */
        return filament(static::make()->getId());
    }
}
