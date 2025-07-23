<div
    x-init="
        selectedRecords = [...cachedSelectedRecords]

        $nextTick(() => updateCheckboxSelectability(selectedRecords))

        $watch('selectedRecords', (records, oldRecords) => {
            ;{{-- Prevent duplicate execution of this watcher (Table Alpine Component also registers watcher on 'selectedRecords' --}}
            if (shouldCheckUniqueSelection) {
                return
            }

            if (suppressWatcherForNextCycle) {
                suppressWatcherForNextCycle = false

                return
            }

            if (selectionLimit === 1 && records.length === 2) {
                const previousRecord = records[records.length - 1]

                if (previousRecord) {
                    selectedRecords = [previousRecord]
                }

                return
            }

            ;{{-- Prevent bulk select checkboxes from breaking stuff --}}
            if (selectionLimit !== null && records.length > selectionLimit) {
                suppressWatcherForNextCycle = true
                selectedRecords = [...cachedSelectedRecords]

                return
            }

            cachedSelectedRecords = [...records]
            updateCheckboxSelectability(records)

            if (! requiresSelectionConfirmation) {
                requestAnimationFrame(() => updateFormComponentState())
            }
        })

        $wire.on(
            'filament-table-select::selection-table.select-table-record',
            (record) => {
                if (! Array.isArray(record) || record.length !== 1) {
                    return
                }

                const value = record[0].toString()
                const index = selectedRecords.indexOf(value)

                if (index === -1) {
                    selectedRecords.push(value)
                } else {
                    selectedRecords.splice(index, 1)
                }
            },
        )

        $wire.on('filament-table-select::selection-table.refresh-checkboxes', () =>
            requestAnimationFrame(() => updateCheckboxSelectability(selectedRecords)),
        )
    "
    x-data="{
        suppressWatcherForNextCycle: true,

        updateCheckboxSelectability(records) {
            if (selectionLimit === 1 || selectionLimit === null) {
                return
            }

            const checkboxes = $wire.$el.querySelectorAll('.fi-ta-record-checkbox')
            const limitReached = records.length >= selectionLimit

            checkboxes.forEach(
                (checkbox) =>
                    (checkbox.disabled = ! checkbox.checked && limitReached),
            )
        },
    }"
></div>
