<?php

declare(strict_types=1);

namespace Dvarilek\FilamentTableSelect\Components\View\Concerns;

use Filament\Forms\Components\Field;
use Filament\Resources\Resource;
use Closure;
use Filament\Tables\Table;
use Illuminate\View\View;

/**
 * @mixin Field
 */
trait InteractsWithSelectionTable
{

    /**
     * @var null | Closure | class-string<Resource>
     */
    protected null | Closure | string $tableLocation = null;

    /**
     * @var ?Closure(Table $table): Table
     */
    protected ?Closure $configureSelectionTableUsing = null;

    /**
     * @param  Closure | class-string<Resource> $component
     *
     * @return $this
     */
    public function tableLocation(Closure | string $component): static
    {
        $this->tableLocation = $component;

        return $this;
    }

    /**
     * @param  Closure(Table $table): Table $configureSelectionTableUsing
     *
     * @return $this
     */
    public function configureTableUsing(Closure $configureSelectionTableUsing): static
    {
        $this->configureSelectionTableUsing = $configureSelectionTableUsing;

        return $this;
    }

    /**
     * @return View
     */
    protected function getSelectionTableView(): View
    {
        $statePath = $this->getStatePath();
        $this->persistConfigurationClosure($statePath);

        return view('filament-table-select::selection-table-modal', [
            'state' => array_map(intval(...), is_array($state = $this->getState()) ? $state : [$state]),
            'isMultiple' => $this->isMultiple(),
            'selectionLimit' => $this->getOptionsLimit(),
            'relatedModel' => $this->getRelationship()->getRelated()::class,
            'tableLocation' => $this->evaluate($this->tableLocation),
            'statePath' => $statePath,
        ]);
    }

    /**
     * @param  string $key
     *
     * @return void
     */
    protected function persistConfigurationClosure(string $key): void
    {
        app()->bind($key, fn () => $this->configureSelectionTableUsing);
    }
}
