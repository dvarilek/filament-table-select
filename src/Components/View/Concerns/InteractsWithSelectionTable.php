<?php

declare(strict_types=1);

namespace Dvarilek\FilamentTableSelect\Components\View\Concerns;

use Dvarilek\FilamentTableSelect\Enums\ConfirmationActionPosition;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\Actions\Action;
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
     * @var bool | Closure
     */
    protected bool | Closure $shouldConfirmSelection = false;

    /**
     * @var bool | Closure
     */
    protected bool | Closure $shouldCloseOnSelection = true;

    /**
     * @var ConfirmationActionPosition
     */
    protected ConfirmationActionPosition $confirmationActionPosition = ConfirmationActionPosition::BOTTOM_LEFT;

    /**
     * @var ?Closure
     */
    protected ?Closure $modifySelectionConfirmationActionUsing = null;

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
     * @param  bool | Closure $shouldConfirmSelection
     * @param  null | bool | Closure $shouldCloseOnSelection
     * @param  ?ConfirmationActionPosition $confirmationActionPosition
     *
     * @return $this
     */
    public function shouldConfirmSelection(
        bool | Closure $shouldConfirmSelection = true,
        null | bool | Closure $shouldCloseOnSelection = null,
        ?ConfirmationActionPosition $confirmationActionPosition = null
    ): static
    {
        $this->shouldConfirmSelection = $shouldConfirmSelection;
        $this->shouldCloseOnSelection = $shouldCloseOnSelection ?? $this->shouldCloseOnSelection;
        $this->confirmationActionPosition = $confirmationActionPosition ?? $this->confirmationActionPosition;

        return $this;
    }

    /**
     * @param  bool | Closure $shouldCloseOnSelection
     *
     * @return $this
     */
    public function shouldCloseOnSelection(bool | Closure $shouldCloseOnSelection = true): static
    {
        $this->shouldCloseOnSelection = $shouldCloseOnSelection;
        $this->shouldConfirmSelection = $this->shouldConfirmSelection ?: true;

        return $this;
    }

    /**
     * @param  ConfirmationActionPosition $confirmationActionPosition
     *
     * @return $this
     */
    public function confirmationActionPosition(ConfirmationActionPosition $confirmationActionPosition): static
    {
        $this->confirmationActionPosition = $confirmationActionPosition;

        return $this;
    }

    /**
     * @param  Closure $modifySelectionConfirmationActionUsing
     * @param  ?ConfirmationActionPosition $confirmationActionPosition
     *
     * @return $this
     */
    public function modifySelectionConfirmationActionUsing(
        Closure $modifySelectionConfirmationActionUsing,
        ?ConfirmationActionPosition $confirmationActionPosition = null
    ): static
    {
        $this->modifySelectionConfirmationActionUsing = $modifySelectionConfirmationActionUsing;
        $this->confirmationActionPosition = $confirmationActionPosition ?? $this->confirmationActionPosition;

        return $this;
    }

    /**
     * @return View
     */
    protected function getSelectionTableView(): View
    {
        $statePath = $this->getStatePath();
        $this->persistConfigurationClosure($statePath);

        $initialState = is_array($state = $this->getState()) ? $state : [$state];

        return view('filament-table-select::selection-table-modal', [
            'initialState' => array_map(intval(...), $initialState),
            'selectionLimit' => $this->getSelectionLimit(),
            'relatedModel' => $this->getRelationship()->getRelated()::class,
            'tableLocation' => $this->evaluate($this->tableLocation),
            'shouldConfirmSelection' => $this->evaluate($this->shouldConfirmSelection),
            'shouldCloseOnSelection' => $this->evaluate($this->shouldCloseOnSelection),
            'confirmationActionPosition' => $this->confirmationActionPosition,
            'selectionConfirmationAction' => $this->getSelectionConfirmationAction(),
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

    /**
     * @return Action
     */
    protected function getSelectionConfirmationAction(): Action
    {
        $action = Action::make('selectionConfirmationAction');

        $action = $this->evaluate($this->modifySelectionConfirmationActionUsing, [
            'action' => $action,
        ], [
            Action::class => $action
        ]) ?? $action;

        return $action->alpineClickHandler('updateFormComponentState');
    }
}
