document.addEventListener('livewire:initialized', () => {
    Livewire.on('filament-table-select::table-select.updateTableSelectComponentState', ({ livewireId, statePath }) => {
        if (! livewireId || ! statePath) {
            return;
        }

        let livewireComponent = Livewire.find(livewireId);

        if (! livewireComponent) {
            return;
        }

        livewireComponent.set(statePath, Alpine.store('selectionModalCache').get(statePath));
    });

    Livewire.on('filament-table-select::table-select.updateTableSelectCacheState', ({ statePath, records, limit }) => {
        if (! statePath) {
            return;
        }

        let state = Alpine.store('selectionModalCache').get(statePath);

        if (state.length >= limit && state !== null) {
            if (limit !== 1) {
                return;
            }

            Alpine.store('selectionModalCache').clear(statePath);
        }

        Alpine.store('selectionModalCache').set(statePath, records);
    });
});
