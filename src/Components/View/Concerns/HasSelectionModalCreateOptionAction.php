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

    /**
     * @var bool | Closure
     */
    protected bool $hasCreateOptionActionInSelectionModal = true;

    /**
     * @var bool | Closure
     */
    protected bool $createOptionActionOnlyVisibleInSelectionModal = true;

    /**
     * @var ?Closure
     */
    protected ?Closure $modifySelectionModalCreateOptionActionUsing = null;

    /**
     * @var SelectionModalActionPosition
     */
    protected SelectionModalActionPosition $selectionModalCreateOptionActionPosition = SelectionModalActionPosition::TOP_RIGHT;

    /**
     * @param bool | Closure $hasCreateOptionActionInSelectionModal
     * @param null | bool | Closure  $createOptionActionOnlyVisibleInSelectionModal
     * @param null | Closure | SelectionModalActionPosition $selectionModalCreateOptionActionPosition
     *
     * @return $this
     */
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

    /**
     * @param  bool | Closure $createOptionActionOnlyVisibleInSelectionModal
     *
     * @return $this
     */
    public function createOptionActionOnlyVisibleInSelectionModal(bool | Closure $createOptionActionOnlyVisibleInSelectionModal = true): static
    {
        $this->createOptionActionOnlyVisibleInSelectionModal = $createOptionActionOnlyVisibleInSelectionModal;

        return $this;
    }

    /**
     * @param  SelectionModalActionPosition $selectionModalCreateOptionActionPosition
     *
     * @return $this
     */
    public function selectionModalCreateOptionActionPosition(SelectionModalActionPosition $selectionModalCreateOptionActionPosition): static
    {
        $this->selectionModalCreateOptionActionPosition = $selectionModalCreateOptionActionPosition;

        return $this;
    }

    /**
     * @param  Closure $modifySelectionModalCreateOptionActionUsing
     *
     * @return $this
     */
    public function modifySelectionModalCreateOptionAction(Closure $modifySelectionModalCreateOptionActionUsing): static
    {
        $this->modifySelectionModalCreateOptionActionUsing = $modifySelectionModalCreateOptionActionUsing;

        return $this;
    }

    /**
     * @return string
     */
    public function getSelectionModalCreateOptionActionName(): string
    {
        return 'selectionModalCreateOptionAction';
    }
}
