<div
    x-init="
        selectedRecords = window.selectedRecords;
        window.selectedRecords = null;

        $watch('selectedRecords', records => {
            updateTableSelectedRecords(records);

            if (shouldConfirmSelection === false) {
                updateFormComponentState();
            }
        });
    "
>
</div>
