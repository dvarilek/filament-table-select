<?php

declare(strict_types=1);

namespace Dvarilek\FilamentTableSelect\Components\View\Concerns;

use Dvarilek\FilamentTableSelect\Enums\SelectionModalActionPosition;
use Dvarilek\FilamentTableSelect\Exceptions\TableSelectException;
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
     * @var bool | Closure
     */
    protected bool | Closure $shouldSelectRecordOnRowClick = true;

    /**
     * @var ?Closure(Table $table): Table
     */
    protected ?Closure $modifySelectionTableUsing = null;

    /**
     * @var bool | Closure
     */
    protected bool | Closure $requiresSelectionConfirmation = false;

    /**
     * @var bool | Closure
     */
    protected bool | Closure $shouldCloseOnSelection = true;

    /**
     * @var SelectionModalActionPosition
     */
    protected SelectionModalActionPosition $confirmationActionPosition = SelectionModalActionPosition::BOTTOM_LEFT;

    /**
     * @var ?Closure
     */
    protected ?Closure $modifySelectionConfirmationActionUsing = null;

    /**
     * @param  Closure | class-string<Resource> $resource
     *
     * @return $this
     */
    public function tableLocation(Closure | string $resource): static
    {
        $this->tableLocation = $resource;

        return $this;
    }

    /**
     * @param  bool | Closure $shouldSelectRecordOnRowClick
     *
     * @return $this
     */
    public function shouldSelectRecordOnRowClick(bool | Closure $shouldSelectRecordOnRowClick): static
    {
        $this->shouldSelectRecordOnRowClick = $shouldSelectRecordOnRowClick;

        return $this;
    }

    /**
     * @param  Closure(Table $table): Table $modifySelectionTableUsing
     *
     * @return $this
     */
    public function modifySelectionTable(Closure $modifySelectionTableUsing): static
    {
        $this->modifySelectionTableUsing = $modifySelectionTableUsing;

        return $this;
    }

    /**
     * @param  bool | Closure $requiresSelectionConfirmation
     * @param  null | bool | Closure $shouldCloseOnSelection
     * @param  null | Closure | SelectionModalActionPosition $confirmationActionPosition
     *
     * @return $this
     */
    public function requiresSelectionConfirmation(
        bool | Closure $requiresSelectionConfirmation = true,
        null | bool | Closure $shouldCloseOnSelection = null,
        null | Closure | SelectionModalActionPosition $confirmationActionPosition = null
    ): static
    {
        $this->requiresSelectionConfirmation = $requiresSelectionConfirmation;
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
        $this->requiresSelectionConfirmation = $this->requiresSelectionConfirmation ?: true;

        return $this;
    }

    /**
     * @param  Closure | SelectionModalActionPosition $confirmationActionPosition
     *
     * @return $this
     */
    public function confirmationActionPosition(Closure | SelectionModalActionPosition $confirmationActionPosition): static
    {
        $this->confirmationActionPosition = $confirmationActionPosition;

        return $this;
    }

    /**
     * @param  Closure $modifySelectionConfirmationActionUsing
     * @param  null | Closure | SelectionModalActionPosition $confirmationActionPosition
     *
     * @return $this
     */
    public function modifySelectionConfirmationAction(
        Closure $modifySelectionConfirmationActionUsing,
        null | Closure | SelectionModalActionPosition $confirmationActionPosition = null
    ): static
    {
        $this->modifySelectionConfirmationActionUsing = $modifySelectionConfirmationActionUsing;
        $this->confirmationActionPosition = $confirmationActionPosition ?? $this->confirmationActionPosition;

        return $this;
    }

    /**
     * @return int
     */
    abstract public function getSelectionLimit(): int;

    /**
     * @return View
     * @throws TableSelectException
     */
    protected function getSelectionTableView(): View
    {
        $state = is_array($state = $this->getState()) ? $state : [$state];
        $selectionLimit = $this->getSelectionLimit();

        if (count($state) > 1 && $selectionLimit === 1) {
            throw TableSelectException::stateCountSurpassesSelectionLimit($state);
        }

        return view($this->selectionTableModalView, [
            'initialState' => $state,
            'selectionLimit' => $selectionLimit,
            'shouldSelectRecordOnRowClick' => $this->evaluate($this->shouldSelectRecordOnRowClick),
            'relatedModel' => $this->getRelationship()->getRelated()::class,
            'tableLocation' => $this->evaluate($this->tableLocation),
            'requiresSelectionConfirmation' => $this->evaluate($this->requiresSelectionConfirmation),
            'shouldCloseOnSelection' => $this->evaluate($this->shouldCloseOnSelection),
            'confirmationActionPosition' => $this->evaluate($this->confirmationActionPosition),
            'selectionConfirmationAction' => $this->getAction($this->getSelectionConfirmationActionName()),
            'modifySelectionTableUsing' => $this->modifySelectionTableUsing,
            'statePath' => $this->getStatePath(),
        ]);
    }

    /**
     * @return string
     */
    public function getSelectionConfirmationActionName(): string
    {
        return 'selectionConfirmationAction';
    }

    /**
     * @return void
     */
    public function updateTableSelectComponentState(): void
    {
        $livewire = $this->getLivewire();

        $livewire->dispatch('filament-table-select::table-select.updateTableSelectComponentState',
            livewireId: $livewire->getId(),
            statePath: $this->getStatePath()
        );
    }

    /**
     * @return Action
     */
    protected function getSelectionConfirmationAction(): Action
    {
        $action = Action::make($this->getSelectionConfirmationActionName())
            ->label(__('filament-table-select::table-select.actions.selection-confirmation.label'))
            ->action($this->updateTableSelectComponentState(...));

        if ($this->evaluate($this->shouldCloseOnSelection)) {
            $action->cancelParentActions();
        }

        return $this->evaluate($this->modifySelectionConfirmationActionUsing, [
            'action' => $action,
        ], [
            Action::class => $action
        ]) ?? $action;
    }
}
