var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};
OpenEyes.Util = OpenEyes.Util || {};
OpenEyes.UI = OpenEyes.UI || {};

(function (exports, Util, UI) {

    const BaseController = UI.ElementController.MultiRow;

    function ColourVision(options) {
        options = $.extend(true, {}, ColourVision._defaultOptions, options);

        BaseController.call(this, options);
        this.syncAdderMethodOptionsWithSelected();
    }

    Util.inherits(BaseController, ColourVision);

    ColourVision._defaultOptions = {
        adderDialogOptions: {
            listFilter: true,
            filterListId: 'method_id',
            listForFilterId: 'value_id',
        },
        adderIdForMethod: 'method_id' // the data-adder-id attribute on the method form field
    };

    /**
     * Removes the test methods from the adder dialog that are already selected
     */
    ColourVision.prototype.syncAdderMethodOptionsWithSelected = function()
    {
        const selectedMethods = [].map.call(
            this.options.container.querySelector(this.options.rowsContainerSelector)
                .querySelectorAll('[data-adder-id="' + this.options.adderIdForMethod + '"]'),
            function(formField) { return formField.value; });

        [].forEach.call(
            this.getItemSetColFromAdder(this.options.adderIdForMethod)
                .querySelectorAll('li'),
            function(option) {
                // because adder dialog is using jquery, we revert to it here for managing option visibility
                if (selectedMethods.includes(option.dataset.id)) {
                    $(option).hide();
                } else {
                    $(option).show();
                }
            });
    };

    ColourVision.prototype.updateFromAdder = function(adderDialog, selectedItems)
    {
        ColourVision._super.prototype.updateFromAdder.call(this, adderDialog, selectedItems);

        this.syncAdderMethodOptionsWithSelected();
    };

    ColourVision.prototype.onRowRemoved = function()
    {
        this.syncAdderMethodOptionsWithSelected();
    };

    exports.ColourVisionController = ColourVision;
})(OpenEyes.OphCiExamination, OpenEyes.Util, OpenEyes.UI);
