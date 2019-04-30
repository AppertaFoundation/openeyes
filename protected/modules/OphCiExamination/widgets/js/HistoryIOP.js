function addDatePicker(datePickerInputs) {
    for (let i = 0; i < datePickerInputs.length; i++) {
        pickmeup(datePickerInputs[i], {
            format: 'Y-m-d',
            hide_on_select: true,
            default_date: false,
            max:  new Date(),
        });
    }
}

$(document).ready(function () {
    function deleteReading() {
        var table = $(this).closest('table');
        if (table.find('tbody tr').length <= 1)
            table.hide();

        $(this).closest('tr').remove();

        return false;
    }

    $("#OEModule_OphCiExamination_models_HistoryIOP_readings_right").on("click", "i.trash", null, deleteReading);
    $("#OEModule_OphCiExamination_models_HistoryIOP_readings_left").on("click", "i.trash", null, deleteReading);
});