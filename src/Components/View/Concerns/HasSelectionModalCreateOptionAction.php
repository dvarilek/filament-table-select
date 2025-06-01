<?php

declare(strict_types=1);

namespace Dvarilek\FilamentTableSelect\Components\View\Concerns;

use Dvarilek\FilamentTableSelect\Enums\SelectionModalActionPosition;
use Filament\Forms\Components\Field;
use Closure;

/**
 * @mixin Field
 */
trait HasSelectionModalCreateOptionAction
{
    protected bool | Closure $hasCreateOptionActionInSelectionModal = true;

    protected bool | Closure $createOptionActionOnlyVisibleInSelectionModal = true;

    protected ?Closure $modifySelectionModalCreateOptionActionUsing = null;

    protected SelectionModalActionPosition $selectionModalCreateOptionActionPosition = SelectionModalActionPosition::TOP_RIGHT;

    public function hasCreateOptionActionInSelectionModal(
        bool | Closure $hasCreateOptionActionInSelectionModal = true,
        null | bool | Closure $createOptionActionOnlyVisibleInSelectionModal = null,
        null | Closure | SelectionModalActionPosition $selectionModalCreateOptionActionPosition = null
    ): static
    {
        $this->hasCreateOptionActionInSelectionModal = $hasCreateOptionActionInSelectionModal;
        $this->createOptionActionOnlyVisibleInSelectionModal = $createOptionActionOnlyVisibleInSelectionModal ?? $this->createOptionActionOnlyVisibleInSelectionModal;
        $this->selectionModalCreateOptionActionPosition = $selectionModalCreateOptionActionPosition ?? $this->selectionModalCreateOptionActionPosition;

        return $this;
    }

    public function createOptionActionOnlyVisibleInSelectionModal(bool | Closure $createOptionActionOnlyVisibleInSelectionModal = true): static
    {
        $this->createOptionActionOnlyVisibleInSelectionModal = $createOptionActionOnlyVisibleInSelectionModal;

        return $this;
    }

    public function selectionModalCreateOptionActionPosition(SelectionModalActionPosition $selectionModalCreateOptionActionPosition): static
    {
        $this->selectionModalCreateOptionActionPosition = $selectionModalCreateOptionActionPosition;

        return $this;
    }

    public function modifySelectionModalCreateOptionAction(Closure $modifySelectionModalCreateOptionActionUsing): static
    {
        $this->modifySelectionModalCreateOptionActionUsing = $modifySelectionModalCreateOptionActionUsing;

        return $this;
    }

    public function getSelectionModalCreateOptionActionName(): string
    {
        return 'selectionModalCreateOptionAction';
    }
}
