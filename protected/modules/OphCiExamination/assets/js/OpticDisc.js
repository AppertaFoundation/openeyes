var rightCdSelector = '#OEModule_OphCiExamination_models_Element_OphCiExamination_OpticDisc_right_cd_ratio_id';
var leftCdSelector = '#OEModule_OphCiExamination_models_Element_OphCiExamination_OpticDisc_left_cd_ratio_id';

function OphCiExamination_OpticDisc_updateCDRatio(field) {
    var cdratio_field = $(field).closest('.eyedraw-fields').find('.cd-ratio');
    var _drawing = ED.getInstance($(field).closest('.js-element-eye').find('canvas').first().attr('data-drawing-name'));
    let data_side = $(field).closest('.js-element-eye').data('side');
    let cd_ratio_readonly_id = 'cd-ratio-readonly-' + data_side;
    $('#' + cd_ratio_readonly_id).remove();
    if($(field).val() == 'Basic') {
        _drawing.unRegisterForNotifications(this);
        cdratio_field.show();
    } else {
        cdratio_field.hide();
        let readonly = $('<span id="' + cd_ratio_readonly_id + '"></span>');
        readonly.html($('option:selected', cdratio_field).attr('data-value'));
        cdratio_field.after(readonly);
        _drawing.registerForNotifications(this, 'handler', ['parameterChanged']);
        this.handler = function(_messageArray) {
            if(_messageArray.eventName == 'parameterChanged' && _messageArray.object.parameter == 'cdRatio') {
                if (_messageArray.object.doodle.drawing.drawingName.includes('left')) {
                    $('#cd-ratio-readonly-left').html(_messageArray.object.value);
                } else {
                    $('#cd-ratio-readonly-right').html(_messageArray.object.value);
                }
            }
        };
    }
}

function OphCiExamination_OpticDisc_init() {
    // cache values loaded with to support reset of eyedraw
    var right_cd = $(rightCdSelector);
    right_cd.data('original', right_cd.val());
    var left_cd = $(leftCdSelector);
    left_cd.data('original', left_cd.val());

    func = function() {
        $('#event-content .Element_OphCiExamination_OpticDisc .opticdisc-mode').each(function() {
            OphCiExamination_OpticDisc_updateCDRatio(this);
        });
    }
    ED.Checker.onAllReady(func);
    // edChecker = getOEEyeDrawChecker();
    // edChecker.registerForReady(func);
}

$(document).ready(function() {
    $(this).delegate('#event-content .' + OE_MODEL_PREFIX + 'Element_OphCiExamination_OpticDisc .opticdisc-mode', 'change', function() {
        OphCiExamination_OpticDisc_updateCDRatio(this);
    });
});

/**
 * This should be converted to the controller paradigm, but wanted to implement a quick
 * fix for ensuring the CD Ratio resets correctly.
 *
 * @param _drawing
 */
function opticDiscListener(_drawing) {
    this.drawing = _drawing;
    this.side = (_drawing.eye === 1 ? 'left' : 'right');

    this.resetOpticDisc = function (_messageArray) {
        var obj = _messageArray.object;
        var cd = $(rightCdSelector);
        if (this.side !== 'right') {
            cd = $(leftCdSelector);
        }
        cd.val(cd.data('original'));
    };

    _drawing.registerForNotifications(this, 'resetOpticDisc', ['reset', 'resetEdit']);
}
