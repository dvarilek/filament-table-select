<div
    x-data="{
        shouldConfirmSelection: @js($shouldConfirmSelection),
        statePath: @js($statePath),
        selectionLimit: @js($selectionLimit),
        cachedSelectedRecords: @js($initialState),

        updateFormComponentState() {
            $wire.set(this.statePath, this.cachedSelectedRecords);

            if (this.shouldConfirmSelection && @js($shouldCloseOnSelection)) {
                close();
            }
        }
    }"
>
    @if (($createAction ?? null) && $createActionPosition->isTop())
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

    @if ($shouldConfirmSelection && $confirmationActionPosition->isTop())
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
            :configureSelectionTableUsing="$configureSelectionTableUsing"
    />

    @if (($createAction ?? null) && $createActionPosition->isBottom())
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

    @if ($shouldConfirmSelection && $confirmationActionPosition->isBottom())
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
