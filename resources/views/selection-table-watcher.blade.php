<div
    x-init="
        selectedRecords = [...cachedSelectedRecords];

        $watch('selectedRecords', records => {
            {{-- Prevent bulk select checkboxes from breaking stuff --}}
            if (records.length > (selectionLimit === 1 ? 2 : selectionLimit)) {
                selectedRecords = [...cachedSelectedRecords];
                return;
            }

            cachedSelectedRecords = selectedRecords;
            resolveCheckboxesSelectability(records);

            if (shouldConfirmSelection === false) {
                updateFormComponentState();
            }

            cachedSelectedRecords = [...selectedRecords];
        });
    "

    x-data="{
        checkboxes: [],

        resolveCheckboxesSelectability(records) {
            this.checkboxes = $wire.$el.querySelectorAll('.fi-ta-record-checkbox');

            if (selectionLimit === 1) {
                if (records.length > 1) {
                    requestAnimationFrame(() => selectedRecords = [records.at(-1)]);
                }
            } else {
                const selectionLimitReached = records.length >= selectionLimit;

                this.checkboxes.forEach(checkbox => checkbox.disabled = !(checkbox.checked || !selectionLimitReached));
            }
        }
    }"
>
</div>

