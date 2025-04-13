<div
    x-data="{
        statePath: @js($statePath),
        livewireId: @js($this->getId()),

        updateFormComponentState(records) {
            if (!this.statePath || !this.livewireId) {
                return;
            }

             const component = Livewire.find(this.livewireId);

             if (! component) {
                return;
             }

             component.set(this.statePath, records)
        },
    }"
>
    <livewire:filament-table-select::selection-table-component
            :initialState="$state"
            :isMultiple="$isMultiple"
            :selectionLimit="$selectionLimit"
            :relatedModel="$relatedModel"
            :tableLocation="$tableLocation"
            :componentIdentifier="$statePath"
    />
</div>
