var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};
OpenEyes.Util = OpenEyes.Util || {};
OpenEyes.UI = OpenEyes.UI || {};

(function (exports, Util, UI) {

    const BaseController = UI.ElementController.MultiRow;

    function CoverAndPrismCover(options) {
        options = $.extend(true, {}, CoverAndPrismCover._defaultOptions, options);

        BaseController.call(this, options);
    }

    Util.inherits(BaseController, CoverAndPrismCover);

    CoverAndPrismCover._defaultOptions = {
        adderDialogOptions: {
            deselectOnReturn: true
        },
        editSelector: '.js-edit-row-btn', // dom selector for the button to edit a row
        editRowClosestSelector: 'tr', // dom selector for finding the row to be edited when clicking the edit button
    };

    CoverAndPrismCover.prototype.initialiseFormDisplay = function(formContainer)
    {
        if (formContainer === undefined) {
            formContainer = this.options.container;
        }

        CoverAndPrismCover._super.prototype.initialiseFormDisplay.call(this, formContainer);

        // row edit event handling
        formContainer.addEventListener('click', function(event) {
            if (!event.target.matches(this.options.editSelector)) return;

            event.preventDefault();
            this.editRow(event.target.closest(this.options.editRowClosestSelector));
        }.bind(this));
    };

    CoverAndPrismCover.prototype.onAdderClose = function(adderDialog)
    {
        // ensure that if it's been opened for an edit, we reset
        this.resetCurrentEditRow();
    };

    CoverAndPrismCover.prototype.resetCurrentEditRow = function()
    {
        if (this.currentEditRow) {
            this.adder.options.itemSets.forEach(
                itemSet => this.adder.setSelectedItemsForItemSet(itemSet, undefined)
            );
            // reset popup anchor for standard adder positioning
            this.adder.options.popupAnchor = $(this.getAdderOpenButton());
            this.currentEditRow = undefined;
        }
    };

    CoverAndPrismCover.prototype.updateCurrentEditRowFromEditDialog = function(adderDialog)
    {
        this.extractFormFields(this.currentEditRow).forEach(function(formField, index) {
            this.setFormFieldFromAdderDialog(adderDialog, index, formField);
        }.bind(this));

        this.resetCurrentEditRow();
    };

    /**
     * Override to handle editing
     *
     * @param adderDialog
     * @param selectedItems
     * @returns {void|*}
     */
    CoverAndPrismCover.prototype.updateFromAdder = function(adderDialog, selectedItems)
    {
        if (this.currentEditRow) {
            return this.updateCurrentEditRowFromEditDialog(adderDialog);
        }

        return CoverAndPrismCover._super.prototype.updateFromAdder.call(this, adderDialog, selectedItems);
    };

    /**
     * Extend to handle listening for edit clicks on the row
     *
     * @param adderDialog
     * @param row
     */
    CoverAndPrismCover.prototype.updateRowFromAdder = function (adderDialog, row)
    {
        CoverAndPrismCover._super.prototype.updateRowFromAdder.call(this, adderDialog, row);

        row.querySelector(this.options.editSelector)
            .addEventListener('click', function(event) {
                event.preventDefault();
                this.editRow(row);
            }.bind(this));
    };

    CoverAndPrismCover.prototype.editRow = function(row)
    {
        if (this.currentEditRow) {
            return;
        }
        this.currentEditRow = row;
        this.openEditDialog();
    };

    CoverAndPrismCover.prototype.openEditDialog = function()
    {
        // use jQuery here for compatibility with the adderDialog itself
        this.adder.options.popupAnchor = $(this.currentEditRow).find(this.options.editSelector);
        this.extractFormFields(this.currentEditRow)
            .forEach((field, index) =>
                this.adder.setSelectedItemsForItemSet(
                    this.adder.options.itemSets[index],
                    field.value)
            );
        this.adder.open();
    };

    exports.CoverAndPrismCoverController = CoverAndPrismCover;

})(OpenEyes.OphCiExamination, OpenEyes.Util, OpenEyes.UI);