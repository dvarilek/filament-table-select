<div
    x-data="{
        shouldConfirmSelection: @js($shouldConfirmSelection),
        statePath: @js($statePath),
        livewireId: @js($this->getId()),
        tableSelectedRecords: [],

        updateTableSelectedRecords(records) {
            if (!this.statePath || !this.livewireId) {
                return;
            }

            this.tableSelectedRecords = records;
        },

        updateFormComponentState() {
            const component = Livewire.find(this.livewireId);

            if (!component || !this.statePath || !this.livewireId) {
                return;
            }

            component.set(this.statePath, this.tableSelectedRecords);

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
            :initialState="$state"
            :isMultiple="$isMultiple"
            :selectionLimit="$selectionLimit"
            :relatedModel="$relatedModel"
            :tableLocation="$tableLocation"
            :componentIdentifier="$statePath"
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
