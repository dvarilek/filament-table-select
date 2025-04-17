<div
    x-data="{
        shouldConfirmSelection: @js($shouldConfirmSelection),
        statePath: @js($statePath),
        livewireId: @js($this->getId()),
        selectionLimit: @js($selectionLimit),
        cachedSelectedRecords: @js($initialState),

        updateFormComponentState() {
            const component = Livewire.find(this.livewireId);

            if (!component || !this.statePath || !this.livewireId) {
                return;
            }

            component.set(this.statePath, this.cachedSelectedRecords);

            if (this.shouldConfirmSelection && @js($shouldCloseOnSelection)) {
                close();
            }
        }
    }"
>
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
            :relatedModel="$relatedModel"
            :tableLocation="$tableLocation"
            :configureSelectionTableUsing="$configureSelectionTableUsing"
    />

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
