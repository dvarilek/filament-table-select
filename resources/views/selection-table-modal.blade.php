<div
    x-data="{
        requiresSelectionConfirmation: @js($requiresSelectionConfirmation),
        statePath: @js($statePath),
        selectionLimit: @js($selectionLimit),
        cachedSelectedRecords: @js($initialState),

        updateFormComponentState() {
            $wire.set(this.statePath, this.cachedSelectedRecords);

            if (this.requiresSelectionConfirmation && @js($shouldCloseOnSelection)) {
                close();
            }
        }
    }"
>
    @if (($createAction ?? null) && $createActionPosition->isTop() && ! $createAction->isDisabled())
        <div
            @class([
                'pb-6 flex',
                'justify-end' => $createActionPosition->isRight(),
                'justify-start' => $createActionPosition->isLeft()
            ])
        >
            {{ $createAction }}
        </div>
    @endif

    @if ($requiresSelectionConfirmation && $confirmationActionPosition->isTop() && ! $selectionConfirmationAction->isDisabled())
        <div
            @class([
                'pb-6 flex',
                'justify-end' => $confirmationActionPosition->isRight(),
                'justify-start' => $confirmationActionPosition->isLeft()
            ])
        >
            {{ $selectionConfirmationAction }}
        </div>
    @endif

    <livewire:filament-table-select::selection-table-component
            :shouldSelectRecordOnRowClick="$shouldSelectRecordOnRowClick"
            :relatedModel="$relatedModel"
            :tableLocation="$tableLocation"
            :modifySelectionTableUsing="$modifySelectionTableUsing"
    />

    @if (($createAction ?? null) && $createActionPosition->isBottom() && ! $createAction->isDisabled())
        <div
            @class([
                'pt-6 flex',
                'justify-end' => $createActionPosition->isRight(),
                'justify-start' => $createActionPosition->isLeft()
            ])
        >
            {{ $createAction }}
        </div>
    @endif

    @if ($requiresSelectionConfirmation && $confirmationActionPosition->isBottom() && ! $selectionConfirmationAction->isDisabled())
        <div
            @class([
                'pt-6 flex',
                'justify-end' => $confirmationActionPosition->isRight(),
                'justify-start' => $confirmationActionPosition->isLeft()
            ])
        >
            {{ $selectionConfirmationAction }}
        </div>
    @endif
</div>
