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

function getScaleDropdown(element_name, instrument_id, scale_td, index, side){
    $.ajax({
        'type': 'GET',
        'url': baseUrl + '/OphCiExamination/default/getScaleForInstrument?name=' + element_name +
            '&instrument_id=' + instrument_id + '&side=' + side + '&index=' + index,
        'success': function (html) {
            if (html.length > 0) {
                scale_td.html(html);
                scale_td.show();
                scale_td.prev('td').hide();
            } else {
                scale_td.html('');
                scale_td.hide();
                scale_td.prev('td').show();
            }
        }
    });
}