@props([
    'initialState',
    'selectionLimit',
    'isMultiple',
    'isDisabled',
    'shouldSelectRecordOnRowClick',
    'model',
    'record',
    'relationshipName',
    'tableLocation',
    'requiresSelectionConfirmation',
    'confirmationActionPosition',
    'selectionConfirmationAction',
    'modifySelectionTableUsing',
    'selectionTableArguments',
    'selectionTableLivewire',
    'statePath',
    'createAction',
    'createActionPosition',
])

@php
    $canRenderCreateAction = ! $isDisabled && $createAction && ! $createAction->isDisabled();
    $canRenderSelectionConfirmationAction = ! $isDisabled && $requiresSelectionConfirmation && ! $selectionConfirmationAction->isDisabled();
@endphp

<div
    x-data="{
        requiresSelectionConfirmation: @js($requiresSelectionConfirmation),
        statePath: @js($statePath),
        selectionLimit: @js($selectionLimit),

        init() {
            if ($store.selectionModalCache.get(this.statePath) === null) {
                this.cachedSelectedRecords = @js($initialState)
            }
        },

        get cachedSelectedRecords() {
            return $store.selectionModalCache.get(this.statePath) ?? []
        },

        set cachedSelectedRecords(records) {
            $store.selectionModalCache.set(this.statePath, records)
        },

        updateFormComponentState() {
            let cachedRecords = this.cachedSelectedRecords

            if (
                ! @js($isMultiple) &&
                Array.isArray(cachedRecords) &&
                cachedRecords.length === 0
            ) {
                cachedRecords = null
            }

            $wire.set(this.statePath, cachedRecords)
        },
    }"
>
    @if ($canRenderCreateAction && $createActionPosition->isTop())
        <div
            @class([
                'flex pb-6',
                'justify-end' => $createActionPosition->isRight(),
                'justify-start' => $createActionPosition->isLeft(),
            ])
        >
            {{ $createAction }}
        </div>
    @endif

    @if ($canRenderSelectionConfirmationAction && $confirmationActionPosition->isTop())
        <div
            @class([
                'flex pb-6',
                'justify-end' => $confirmationActionPosition->isRight(),
                'justify-start' => $confirmationActionPosition->isLeft(),
            ])
        >
            {{ $selectionConfirmationAction }}
        </div>
    @endif

    @livewire($selectionTableLivewire, [
        'shouldSelectRecordOnRowClick' => $shouldSelectRecordOnRowClick,
        'isDisabled' => $isDisabled,
        'model' => $model,
        'record' => $record,
        'relationshipName' => $relationshipName,
        'tableLocation' => $tableLocation,
        'modifySelectionTableUsing' => $modifySelectionTableUsing,
        'arguments' => $selectionTableArguments,
    ])

    @if ($canRenderCreateAction && $createActionPosition->isBottom())
        <div
            @class([
                'flex pt-6',
                'justify-end' => $createActionPosition->isRight(),
                'justify-start' => $createActionPosition->isLeft(),
            ])
        >
            {{ $createAction }}
        </div>
    @endif

    @if ($canRenderSelectionConfirmationAction && $confirmationActionPosition->isBottom())
        <div
            @class([
                'flex pt-6',
                'justify-end' => $confirmationActionPosition->isRight(),
                'justify-start' => $confirmationActionPosition->isLeft(),
            ])
        >
            {{ $selectionConfirmationAction }}
        </div>
    @endif
</div>
