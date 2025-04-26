document.addEventListener('livewire:initialized', () => {
    Livewire.on('updateTableSelectState', ({ livewireId, statePath }) => {
        if (! livewireId || ! statePath) {
            return;
        }

        livewireComponent = Livewire.find(livewireId);

        if (! livewireComponent) {
            return;
        }

        livewireComponent.set(statePath, Alpine.store('selectionModalCache').get(statePath));
    });
});
