<?php

declare(strict_types=1);

namespace Dvarilek\FilamentTableSelect\Components\Form\Concerns;

use Dvarilek\FilamentTableSelect\Components\Livewire\SelectionTable;
use Dvarilek\FilamentTableSelect\Enums\SelectionModalActionPosition;
use Dvarilek\FilamentTableSelect\Exceptions\TableSelectException;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Resources\Resource;
use Illuminate\Support\Js;
use Illuminate\Contracts\View\View;
use Closure;
use Livewire\Component;

/**
 * @mixin Field
 */
trait HasSelectionTable
{
    /**
     * @var class-string<mixed | Resource> | Closure | null
     */
    protected  string | Closure | null $tableLocation = null;

    protected bool | Closure $shouldSelectRecordOnRowClick = true;

    protected ?Closure $modifySelectionTableUsing = null;

    protected bool | Closure $requiresSelectionConfirmation = false;

    protected bool | Closure $shouldCloseAfterSelection = true;

    protected SelectionModalActionPosition | Closure $confirmationActionPosition = SelectionModalActionPosition::BOTTOM_LEFT;

    protected ?Closure $modifySelectionConfirmationActionUsing = null;

    protected ?Closure $selectionTableArguments = null;

    /**
     * @var string<SelectionTable> | Closure | null
     */
    protected string | Closure | null $selectionTableLivewire = null;

    abstract public function getSelectionLimit(): ?int;

    abstract public function isMultiple(): bool;

    public function tableLocation(Closure | string $resource): static
    {
        $this->tableLocation = $resource;

        return $this;
    }

    public function shouldSelectRecordOnRowClick(bool | Closure $shouldSelectRecordOnRowClick): static
    {
        $this->shouldSelectRecordOnRowClick = $shouldSelectRecordOnRowClick;

        return $this;
    }

    public function selectionTable(?Closure $callback): static
    {
        $this->modifySelectionTableUsing = $callback;

        return $this;
    }

    public function requiresSelectionConfirmation(
        bool | Closure $requiresSelectionConfirmation = true,
        bool | Closure | null $shouldCloseAfterSelection = null,
        SelectionModalActionPosition | Closure | null $confirmationActionPosition = null
    ): static
    {
        $this->requiresSelectionConfirmation = $requiresSelectionConfirmation;
        $this->shouldCloseAfterSelection = $shouldCloseAfterSelection ?? $this->shouldCloseAfterSelection;
        $this->confirmationActionPosition = $confirmationActionPosition ?? $this->confirmationActionPosition;

        return $this;
    }

    public function selectionTableArguments(array | Closure | null $arguments): static
    {
        $this->selectionTableArguments = $arguments;

        return $this;
    }

    /**
     * @param  string<SelectionTable>|Closure|null $livewire
     * @return $this
     */
    public function selectionTableLivewire(string | Closure | null $livewire): static
    {
        $this->selectionTableLivewire = $livewire;

        return $this;
    }

    public function shouldCloseAfterSelection(bool | Closure $shouldCloseAfterSelection = true): static
    {
        $this->shouldCloseAfterSelection = $shouldCloseAfterSelection;

        return $this;
    }

    public function confirmationActionPosition(Closure | SelectionModalActionPosition $confirmationActionPosition): static
    {
        $this->confirmationActionPosition = $confirmationActionPosition;

        return $this;
    }

    public function selectionConfirmationAction(
        ?Closure $modifySelectionConfirmationActionUsing,
        SelectionModalActionPosition | Closure | null $confirmationActionPosition = null
    ): static
    {
        $this->modifySelectionConfirmationActionUsing = $modifySelectionConfirmationActionUsing;
        $this->confirmationActionPosition = $confirmationActionPosition ?? $this->confirmationActionPosition;

        return $this;
    }

    protected function getSelectionModalView(): View
    {
        $state = $this->getState();
        $state = is_array($state) || is_null($state) ? $state : [$state];

        $selectionLimit = $this->getSelectionLimit();

        if (! is_null($state) && count($state) > 1 && $selectionLimit === 1) {
            throw TableSelectException::stateCountSurpassesSelectionLimit($state);
        }

        $selectionTableLivewire = $this->evaluate($this->selectionTableLivewire);

        if ($selectionTableLivewire === null) {
            $selectionTableLivewire = SelectionTable::class;
        } elseif (! is_subclass_of($selectionTableLivewire, SelectionTable::class)) {
            throw TableSelectException::incorrectSelectionTableLivewireComponent();
        }

        return view($this->selectionTableModalView, [
            'initialState' => $state,
            'selectionLimit' => $selectionLimit,
            'isMultiple' => $this->isMultiple(),
            'isDisabled' => $this->isDisabled(),
            'shouldSelectRecordOnRowClick' => $this->evaluate($this->shouldSelectRecordOnRowClick),
            'model' => $this->getModel(),
            'record' => $this->getRecord(),
            'relationshipName' => $this->getRelationshipName(),
            'tableLocation' => $this->evaluate($this->tableLocation),
            'requiresSelectionConfirmation' => $this->evaluate($this->requiresSelectionConfirmation),
            'confirmationActionPosition' => $this->evaluate($this->confirmationActionPosition),
            'selectionConfirmationAction' => $this->getAction($this->getSelectionConfirmationActionName()),
            'modifySelectionTableUsing' => $this->modifySelectionTableUsing,
            'selectionTableArguments' => $this->evaluate($this->selectionTableArguments),
            'selectionTableLivewire' => $selectionTableLivewire,
            'statePath' => $this->getStatePath(),
            'createAction' => $this->getAction($this->getSelectionModalCreateOptionActionName()),
            'createActionPosition' => $this->evaluate($this->createOptionActionPosition),
        ]);
    }

    protected function getSelectionConfirmationAction(): Action
    {
        $action = Action::make($this->getSelectionConfirmationActionName())
            ->label(__('filament-table-select::table-select.actions.selection-confirmation.label'))
            ->action(static function (Component $livewire, Field $component) {
                $statePath = Js::from($component->getStatePath());
                /* @phpstan-ignore-next-line method.notFound */
                $isMultiple = Js::from($component->isMultiple());

                $livewire->js(<<<JS
                    let cachedRecords = Alpine.store('selectionModalCache').get($statePath);

                    if (!$isMultiple && Array.isArray(cachedRecords) && cachedRecords.length === 0) {
                        cachedRecords = null;
                    }

                    \$wire.set($statePath, cachedRecords);
                JS);
            });

        if ($this->evaluate($this->shouldCloseAfterSelection)) {
            $action->cancelParentActions();
        }

        if ($this->modifySelectionConfirmationActionUsing) {
            $action = $this->evaluate($this->modifySelectionConfirmationActionUsing, [
                'action' => $action,
            ], [
                Action::class => $action
            ]) ?? $action;
        }

        return $action;
    }

    public function getSelectionConfirmationActionName(): string
    {
        return 'selectionConfirmationAction';
    }
}
