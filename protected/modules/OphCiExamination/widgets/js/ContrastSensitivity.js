var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};
OpenEyes.Util = OpenEyes.Util || {};
OpenEyes.UI = OpenEyes.UI || {};

(function (exports, Util, UI) {

    const BaseController = UI.ElementController.MultiRow;

    function ContrastSensitivity(options) {
        options = $.extend(true, {}, ContrastSensitivity._defaultOptions, options);
        BaseController.call(this, options);
    }

    Util.inherits(BaseController, ContrastSensitivity);

    ContrastSensitivity._defaultOptions = {
        adderDialogOptions: {
            deselectOnReturn: true
        }
    };

    ContrastSensitivity.prototype.getSelectedValueFromAdderDialog = function (adderDialog, itemSetId) {
        return this.getFormattedItemSetValueFromAdderDialog(adderDialog,
            this.getItemSetForId(adderDialog, itemSetId),
            {});
    };

    ContrastSensitivity.prototype.getItemSetForId = function (adderDialog, itemSetId) {
        return [].filter.call(adderDialog.options.itemSets, function (itemSet) {
            return itemSet.options.id === itemSetId;
        })[0];
    };

    ContrastSensitivity.prototype.updateFromAdder = function (adderDialog, selectedItems) {
        console.log(["UpperFromAdder", adderDialog, selectedItems]);

        ContrastSensitivity._super.prototype.updateFromAdder.call(this, adderDialog, selectedItems);

        let eyeID = this.getSelectedValueFromAdderDialog(adderDialog, "OEModule_OphCiExamination_models_ContrastSensitivity[results][{{row_count}}]_eye_id");
        this.showLateralityIcon(eyeID);
    };

    ContrastSensitivity.prototype.showLateralityIcon = function (eyeID) {
        let allResultRows = this.options.container.querySelectorAll("table tbody tr");
        let lastResultRow = allResultRows[allResultRows.length - 2];
        let iconContainer = lastResultRow.querySelector(".oe-eye-lat-icons");

        if (eyeID.toString() === "0") {
            this.toggleDomElement(iconContainer.querySelector(".oe-i.R"), true);
            this.toggleDomElement(iconContainer.querySelector(".oe-i.NA"), true);
        } else if (eyeID.toString() === "1") {
            this.toggleDomElement(iconContainer.querySelector(".oe-i.L"), true);
            this.toggleDomElement(iconContainer.querySelector(".oe-i.NA"), true);
        } else if (eyeID.toString() === "2") {
            this.toggleDomElement(iconContainer.querySelector(".oe-i.NO"), true);
            this.toggleDomElement(iconContainer.querySelector(".oe-i.beo"), true);
        }
    };

    exports.ContrastSensitivityController = ContrastSensitivity;

})(OpenEyes.OphCiExamination, OpenEyes.Util, OpenEyes.UI);