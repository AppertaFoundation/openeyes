var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};
OpenEyes.Util = OpenEyes.Util || {};
OpenEyes.UI = OpenEyes.UI || {};

(function (exports, Util, UI) {

    const BaseController = UI.ElementController;

    function BirthHistoryController(options) {
        options = $.extend(true, {}, BirthHistoryController._defaultOptions, options);

        this.weightUnitsField = document.getElementById(options.modelName + '_input_weight_mode');
        this.inputKgsField = document.getElementById(options.modelName + '_input_weight_kgs');
        this.inputLbsPortionField = document.getElementById(options.modelName + '_input_weight_lbs_portion');
        this.inputOzsPortionField = document.getElementById(options.modelName + '_input_weight_ozs_portion');
        this.deliveryField = document.getElementById(options.modelName + '_birth_history_delivery_type_id');
        this.gestationWeeksField = document.getElementById(options.modelName + '_gestation_weeks');
        this.specialistCareField = document.getElementById(options.modelName + '_had_neonatal_specialist_care');
        this.multipleBirthField = document.getElementById(options.modelName + '_was_multiple_birth');

        BaseController.call(this, options);

    }

    Util.inherits(BaseController, BirthHistoryController);

    BirthHistoryController._defaultOptions = {
        modelName: 'OEModule_OphCiExamination_models_BirthHistory',
        nothing_selected_text: "-",
    };

    /**
     * Override to deal with the form fields being in a different order to how they should
     * be displayed in the adder dialog
     *
     * @param formContainer
     * @return {S[] | *[]}
     */
    BirthHistoryController.prototype.extractFormFields = function(formContainer)
    {
        return [
            this.weightUnitsField,
            this.inputKgsField,
            this.inputLbsPortionField,
            this.inputOzsPortionField,
            this.deliveryField,
            this.gestationWeeksField,
            this.specialistCareField,
            this.multipleBirthField
        ];
    };

    /**
     * Show the correct weight input field based on the selected units
     * Relies on the tagging of the different input fields as display-type and input-type
     */
    BirthHistoryController.prototype.setWeightFieldDisplay = function() {
        let showSelector = '[data-weight-type="' + this.weightUnitsField.value + '"]';

        this.options.container
            .querySelectorAll('[data-weight-type]:not(' + showSelector + ')')
            .forEach(function(fld) {
                fld.style.display = 'none';
                fld.disabled = true;

            });
        this.options.container
            .querySelectorAll(showSelector)
            .forEach(function(fld) {
                fld.style.display = 'inline-block';
                fld.disabled = false;
            });
    };

    /**
     * Sets up the triggers to update the displayed values when the form fields are changed.
     */
    BirthHistoryController.prototype.initialiseFormDisplay = function () {
        BirthHistoryController._super.prototype.initialiseFormDisplay.call(this);
        this.weightUnitsField.addEventListener('change', this.setWeightFieldDisplay.bind(this));
        this.setWeightFieldDisplay();
    };

    /**
     * Controller callback for adder dialog opening
     *
     * @param adderDialog
     */
    BirthHistoryController.prototype.onAdderOpen = function(adderDialog) {
        // ensure that numerical values are selected in the adder dialog
        adderDialog.setSelectedItemsForItemSet(adderDialog.options.itemSets[0], this.weightUnitsField.value);
        adderDialog.setSelectedItemsForItemSet(adderDialog.options.itemSets[1], this.inputKgsField.value);
        adderDialog.setSelectedItemsForItemSet(adderDialog.options.itemSets[2], this.inputLbsPortionField.value);
        adderDialog.setSelectedItemsForItemSet(adderDialog.options.itemSets[3], this.inputOzsPortionField.value);
        adderDialog.setSelectedItemsForItemSet(adderDialog.options.itemSets[5], this.gestationWeeksField.value);
    };

    exports.BirthHistoryController = BirthHistoryController;
})(OpenEyes.OphCiExamination, OpenEyes.Util, OpenEyes.UI);