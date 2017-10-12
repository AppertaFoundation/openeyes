$(document).ready(function () {
    function OphCiExamination_Dilation_getNextKey() {
        var keys = $('#event-content .' + OE_MODEL_PREFIX + 'Element_OphCiExamination_Dilation .dilationTreatment').map(function (index, el) {
            return parseInt($(el).attr('data-key'));
        }).get();
        if (keys.length) {
            return Math.max.apply(null, keys) + 1;
        } else {
            return 0;
        }
    }

    function OphCiExamination_Dilation_addTreatment(element, side) {
        var drug_id = $('option:selected', element).val();
        var data_order = $('option:selected', element).data('order');
        if (drug_id) {
            var drug_name = $('option:selected', element).text();
            $('option:selected', element).remove();
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
            var table = $('#event-content .' + OE_MODEL_PREFIX + 'Element_OphCiExamination_Dilation [data-side="' + side + '"] .dilation_table');
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

    $(this).delegate('.dilation_drug', 'change', function (e) {
        var side = $(this).closest('.side').attr('data-side');
        OphCiExamination_Dilation_addTreatment(this, side);
        e.preventDefault();
    });

    $('.dilation_drug').keypress(function (e) {
        if (e.keyCode == 13) {
            var side = $(this).closest('.side').attr('data-side');
            OphCiExamination_Dilation_addTreatment(this, side);
        }
    });

    $(this).delegate('.' + OE_MODEL_PREFIX + 'Element_OphCiExamination_Dilation .removeTreatment', 'click', function (e) {
        var wrapper = $(this).closest('.side');
        var side = wrapper.attr('data-side');
        var row = $(this).closest('tr');
        var data_order = row.attr('data-order');
        var id = $('.drugId', row).val();
        var name = $('.drugName', row).text();
        row.remove();
        var dilation_drug = wrapper.find('.dilation_drug');
        dilation_drug.append('<option value="' + id + '" data-order="' + data_order + '">' + name + '</option>');
        sort_selectbox(dilation_drug);
        if ($('.dilation_table tbody tr', wrapper).length == 0) {
            $('.dilation_table', wrapper).hide();
            $('.timeDiv', wrapper).hide();
        }
        e.preventDefault();
    });

    $(this).delegate('#event-content .' + OE_MODEL_PREFIX + 'Element_OphCiExamination_Dilation .clearDilation', 'click', function (e) {
        var side = $(this).closest('.side').attr('data-side');
        $(this).closest('.side').find('tr.dilationTreatment a.removeTreatment').click();
        e.preventDefault();
    });
});