function addDatePicker(datePickerInputs) {
    for (let i = 0; i < datePickerInputs.length; i++) {
        pickmeup(datePickerInputs[i], {
            format: 'd-m-Y',
            hide_on_select: true,
            default_date: false,
            max:  new Date(),
        });
        // TODO: create a css class for date with width of 90px
        $(datePickerInputs[i]).css('width','90px');
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