document.addEventListener('livewire:initialized', () => {
    Alpine.store('selectionModalCache', {
        data: {},

        get(statePath) {
            return this.data[statePath] ?? null;
        },

        set(statePath, state) {
            this.data[statePath] = state;
        },

        remove(statePath) {
            delete this.data[statePath];
        }
    });
});
