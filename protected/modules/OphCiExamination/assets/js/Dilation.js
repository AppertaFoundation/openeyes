$(document).ready(function () {
    function OphCiExamination_Dilation_getNextKey() {
        var keys = $('.main-event .edit-Dilation .dilationTreatment').map(function (index, el) {
            return parseInt($(el).attr('data-key'));
        }).get();
        if (keys.length) {
            return Math.max.apply(null, keys) + 1;
        } else {
            return 0;
        }
    }

    function OphCiExamination_Dilation_addTreatment(element, side) {
        var drug_id = $(element).attr('data-str');
        var data_order = $(element).attr('data-order');
        if (drug_id) {
            var drug_name = $(element).text();
            var template = $('#dilation_treatment_template').html();
            var data = {
                "key": OphCiExamination_Dilation_getNextKey(),
                "side": side,
                "drug_name": drug_name,
                "drug_id": drug_id,
                "data_order": data_order,
                "treatment_time": (new Date).toTimeString().substr(0, 5)
            };
            var form = Mustache.render(template, data);
            var table = $('.main-event .edit-Dilation .element-eye[data-side="' + side + '"] .dilation_table');
            table.show();
            $(element).closest('.side').find('.timeDiv').show();
            $('tbody', table).append(form);
        }
    }

    $('#' + OE_MODEL_PREFIX + 'Element_OphCiExamination_Dilation_time_right').die('keypress').live('keypress', function (e) {
        if (e.keyCode == 13) {
            return false;
        }
        return true;
    });

    $('#' + OE_MODEL_PREFIX + 'Element_OphCiExamination_Dilation_time_left').die('keypress').live('keypress', function (e) {
        if (e.keyCode == 13) {
            return false;
        }
        return true;
    });

    $('.dilation_drug').keypress(function (e) {
        if (e.keyCode == 13) {
            var side = $(this).closest('.side').attr('data-side');
            OphCiExamination_Dilation_addTreatment(this, side);
        }
    });

    $(this).delegate('.edit-Dilation .removeTreatment', 'click', function (e) {
        var wrapper = $(this).closest('.side');
        var row = $(this).closest('tr');
        row.remove();
        if ($('.dilation_table tbody tr', wrapper).length == 0) {
            $('.dilation_table', wrapper).hide();
            $('.timeDiv', wrapper).hide();
        }
        e.preventDefault();
    });

    $('.main-event .edit-Dilation .add-icon-btn').click(function(e) {
        var side = $(this).closest('.side').attr('data-side');
        var element = $(this).closest('.flex-item-bottom').find('li.selected');
        OphCiExamination_Dilation_addTreatment(element, side);
        e.preventDefault();
    });

    $(this).delegate('.main-event .edit-Dilation .clearDilation', 'click', function (e) {
        $(this).closest('.side').find('tr.dilationTreatment a.removeTreatment').click();
        e.preventDefault();
    });
});