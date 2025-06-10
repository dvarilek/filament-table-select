export default function tableSelect({
    state,
    isMultiple,
}) {
    return {
        state,

        isMultiple,

        isOptionSelected: function (optionKey = null) {
            if (! this.isMultiple && optionKey === null) {
                return this.state !== null;
            }

            if (Array.isArray(this.state)) {
                return this.state.includes(optionKey);
            }

            return false;
        },

        hasSelectedOptions: function () {
            if (! this.isMultiple) {
                return this.state !== null;
            }

            return Array.isArray(this.state) && this.state.length > 0;
        },

        deselectOption: function (optionToDelete = null) {
            if (! this.isMultiple && optionToDelete === null) {
                this.state = null;

                return;
            }

            if (Array.isArray(this.state)) {
                this.state = this.state.filter(key => key !== optionToDelete);
            }
        },
    }
}