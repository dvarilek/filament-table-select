<?php

declare(strict_types=1);

namespace Dvarilek\FilamentTableSelect\Components\Form\Concerns;

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

    protected bool | Closure $shuoldCloseAfterSelection = true;

    protected SelectionModalActionPosition | Closure $confirmationActionPosition = SelectionModalActionPosition::BOTTOM_LEFT;

    protected ?Closure $modifySelectionConfirmationActionUsing = null;

    abstract public function getSelectionLimit(): int;

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
        bool | Closure | null $shuoldCloseAfterSelection = null,
        SelectionModalActionPosition | Closure | null $confirmationActionPosition = null
    ): static
    {
        $this->requiresSelectionConfirmation = $requiresSelectionConfirmation;
        $this->shuoldCloseAfterSelection = $shuoldCloseAfterSelection ?? $this->shuoldCloseAfterSelection;
        $this->confirmationActionPosition = $confirmationActionPosition ?? $this->confirmationActionPosition;

        return $this;
    }

    public function shuoldCloseAfterSelection(bool | Closure $shuoldCloseAfterSelection = true): static
    {
        $this->shuoldCloseAfterSelection = $shuoldCloseAfterSelection;

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

        if ($this->evaluate($this->shuoldCloseAfterSelection)) {
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
