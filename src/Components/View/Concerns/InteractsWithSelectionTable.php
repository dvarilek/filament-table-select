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
     * @return View
     */
    protected function getSelectionTableView(): View
    {
        return view('filament-table-select::selection-table-modal', [
            'isMultiple' => $this->isMultiple(),
            'selectionLimit' => $this->getOptionsLimit(),
            'relatedModel' => $this->getRelationship()->getRelated()::class,
            'tableLocation' => $this->evaluate($this->tableLocation),
            'statePath' => $this->getStatePath(),
        ]);
    }
}
