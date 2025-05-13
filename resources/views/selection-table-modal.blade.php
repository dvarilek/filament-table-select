<div
    x-data="{
        requiresSelectionConfirmation: @js($requiresSelectionConfirmation),
        statePath: @js($statePath),
        selectionLimit: @js($selectionLimit),

        init() {
            {{-- The selection modal cache is required for storing unstaged state between different modal openings, ensuring the selected records don't get wiped before commiting. --}}
            if ($store.selectionModalCache.get(this.statePath) === null) {
                this.cachedSelectedRecords = @js($initialState);
            }
        },

        get cachedSelectedRecords() {
            return $store.selectionModalCache.get(this.statePath) ?? [];
        },

        set cachedSelectedRecords(records) {
            $store.selectionModalCache.set(this.statePath, records);
        },

        updateFormComponentState() {
            $wire.set(this.statePath, this.cachedSelectedRecords);
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
