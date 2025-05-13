document.addEventListener('livewire:initialized', () => {
    Alpine.store('selectionModalCache', {
        data: {},

        get(statePath) {
            return this.data[statePath] ?? null;
        },

        push(statePath, record) {
            if (!Array.isArray(this.data[statePath])) {
                this.data[statePath] = [];
            }

            this.data[statePath].push(record);
        },

        set(statePath, state) {
            this.data[statePath] = state;
        },

        clear(statePath) {
            delete this.data[statePath];
        },
    });
});
