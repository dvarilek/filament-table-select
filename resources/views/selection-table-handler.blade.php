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

        $wire.on('selectTableRecord', record => {
            if (! Array.isArray(record) || record.length !== 1) {
                return;
            }

            const value = record[0].toString();
            const index = selectedRecords.indexOf(value);

            index !== -1 ? selectedRecords.splice(index, 1) : selectedRecords.push(value);
        });

        $wire.on('refreshCheckboxes', () => requestAnimationFrame(() => resolveCheckboxesSelectability(selectedRecords)))
    "

    x-data="{
        resolveCheckboxesSelectability(records) {
            const checkboxes = $wire.$el.querySelectorAll('.fi-ta-record-checkbox');

            if (selectionLimit === 1) {
                if (records.length > 1) {
                    requestAnimationFrame(() => selectedRecords = [records.at(-1)]);
                }
            } else {
                const selectionLimitReached = records.length >= selectionLimit;

                checkboxes.forEach(checkbox => checkbox.disabled = !(checkbox.checked || !selectionLimitReached));
            }
        }
    }"
>
</div>

