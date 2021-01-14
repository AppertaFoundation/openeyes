var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};
OpenEyes.Util = OpenEyes.Util || {};
OpenEyes.UI = OpenEyes.UI || {};

(function (exports, Util, UI) {

    const BaseController = UI.ElementController.MultiRow;

    function StereoAcuity(options) {
        options = $.extend(true, {}, StereoAcuity._defaultOptions, options);

        BaseController.call(this, options);
    }

    Util.inherits(BaseController, StereoAcuity);

    StereoAcuity._defaultOptions = {
        adderDialogOptions: {
            deselectOnReturn: true
        }
    };

    /**
     * If a result is not inconclusive, we should show the result field, otherwise we
     * show in the inconclusive display value.
     *
     * @param formContainer
     */
    StereoAcuity.prototype.onlyDisplayRelevantFieldsForRow = function(formContainer)
    {
        StereoAcuity._super.prototype.onlyDisplayRelevantFieldsForRow.call(this, formContainer);
        const inconclusiveField = formContainer.querySelector('[data-adder-id$="_inconclusive"]');
        if (inconclusiveField.value === "0") {
            this.toggleDomElement(inconclusiveField.previousElementSibling, false);
            const resultField = formContainer.querySelector('[data-adder-id$="_result"]');
            this.toggleDomElement(resultField, true);
            this.toggleDomElement(resultField.previousElementSibling, false);
        }
    };

    exports.StereoAcuityController = StereoAcuity;

})(OpenEyes.OphCiExamination, OpenEyes.Util, OpenEyes.UI);