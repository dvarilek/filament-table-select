<div
    x-init="
        selectedRecords = window.selectedRecords;
        window.selectedRecords = null;

        $watch('selectedRecords', records => updateFormComponentState(records));
    "
>
</div>
