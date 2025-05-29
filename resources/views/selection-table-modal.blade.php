@php
    $canRenderCreateAction = !$isDisabled && $createAction && !$createAction->isDisabled();
    $canRenderSelectionConfirmationAction = !$isDisabled && $requiresSelectionConfirmation && !$selectionConfirmationAction->isDisabled();
@endphp

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
            let cachedRecords = this.cachedSelectedRecords;

            if (!@js($isMultiple) && Array.isArray(cachedRecords) && cachedRecords.length === 0) {
                cachedRecords = null;
            }

            $wire.set(this.statePath, cachedRecords);
        }
    }"
>
    @if ($canRenderCreateAction && $createActionPosition->isTop())
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

    @if ($canRenderSelectionConfirmationAction && $confirmationActionPosition->isTop())
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
            :isDisabled="$isDisabled"
            :relatedModel="$relatedModel"
            :tableLocation="$tableLocation"
            :modifySelectionTableUsing="$modifySelectionTableUsing"
    />

    @if ($canRenderCreateAction && $createActionPosition->isBottom())
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

    @if ($canRenderSelectionConfirmationAction && $confirmationActionPosition->isBottom())
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
