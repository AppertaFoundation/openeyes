$(document).ready(function () {
    $('.iop-date').datepicker({ dateFormat: 'dd/mm/yy', maxDate: '0', showAnim: 'fold'});

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