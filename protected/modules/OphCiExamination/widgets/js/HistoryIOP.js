$(document).ready(function () {
    console.log("ajoiwdjoiajwdoijw");
    $('.iop-date').datepicker({ dateFormat: 'dd/mm/yy', maxDate: '0', showAnim: 'fold'});

    function deleteReading(e) {
        var table = $(this).closest('table');
        if (table.find('tbody tr').length <= 1)
            table.hide();

        $(this).closest('tr').remove();

        return false;
    }

    $("#OEModule_OphCiExamination_models_HistoryIOP_readings_right").on("click", "i.trash", null, deleteReading);
    $("#OEModule_OphCiExamination_models_HistoryIOP_readings_right").on("click", "i.trash", null, deleteReading);

    $('select.IOPinstrument').die('change').live('change', function (e) {
        e.preventDefault();

        var instrument_id = $(this).val();

        var scale_td = $(this).closest('tr').children('td.scale_values');
        var index = $(this).closest('tr').data('index');
        var side = $(this).closest('tr').data('side');

        getScaleDropdown(instrument_id, scale_td, index, side);
    });
});

function getScaleDropdown(instrument_id, scale_td, index, side){
    $.ajax({
        'type': 'GET',
        'url': baseUrl + '/OphCiExamination/default/getScaleForInstrument?name=OEModule_OphCiExamination_models_HistoryIOP' +
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