(function (exports, Util) {
    'use strict';

    // Base Dialog.
    var ElementController = exports;

    function MultiRowElementController(options) {
        options = $.extend(true, {}, MultiRowElementController._defaultOptions, options);

        ElementController.call(this, options);
    }

    Util.inherits(ElementController, MultiRowElementController);

    MultiRowElementController._defaultOptions = {
        adderDialogOptions: {
            deselectOnReturn: true
        },
        templateSelector: '[data-entry-template]', // the dom selector for the template content
        rowsSelector: 'table tbody tr', // the dom selector that contains the completed rows
        rowsContainerSelector: 'table tbody', // where the completed rows live
        trashSelector: '.trash', // dom selector for the button to remove a row
        trashRowClosestSelector: 'tr', // dom selector for finding the row to be removed by clicking on the trash button
    };

    /**
     * Retrieve the template element for this element
     *
     * @return {Element | any}
     */
    MultiRowElementController.prototype.getTemplate = function()
    {
        if (!this.template) {
            this.template = this.options.container.querySelector(this.options.templateSelector);
            if (!this.template) {
                console.log('ERROR: cannot find row template with selector ' + this.options.templateSelector);
            }
        }

        return this.template;
    };

    /**
     * Initialise display for each row
     *
     * @param formContainer
     */
    MultiRowElementController.prototype.initialiseFormDisplay = function(formContainer)
    {
        if (formContainer === undefined) {
            formContainer = this.options.container;
        }

        formContainer.querySelectorAll(this.options.rowsSelector)
            .forEach(function(row) {
                this.extractFormFields(row)
                    .forEach(function(formField) {
                        this.setupFieldDisplaySync(formField);
                    }.bind(this));
                this.onlyDisplayRelevantFieldsForRow(row);
            }.bind(this));

        // row removal event handling
        formContainer.addEventListener('click', function(event) {
            if (!event.target.matches(this.options.trashSelector)) return;

            event.preventDefault();
            event.target.closest(this.options.trashRowClosestSelector).remove();
            this.onRowRemoved();
        }.bind(this));
    };

    /**
     * Override ElementController to get it to use the template to define the adder form fields
     */
    MultiRowElementController.prototype.defineAdderItemSets = function(formContainer)
    {
        return MultiRowElementController._super.prototype.defineAdderItemSets.call(this, this.getTemplate().content);
    };

    MultiRowElementController.prototype.createRowFromTemplate = function()
    {
        let templateText = this.getTemplate().innerHTML;
        let data = {
            'row_count': OpenEyes.Util.getNextDataKey('#' + this.options.container.getAttribute('id') + ' table tr', 'key')
        };

        return Mustache.render(templateText, data);
    };

    /**
     * Override to process template
     *
     * @param adderDialog
     * @param selectedItems
     */
    MultiRowElementController.prototype.updateFromAdder = function(adderDialog, selectedItems)
    {
        let rowContainer = this.options.container.querySelector(this.options.rowsContainerSelector);
        rowContainer.insertAdjacentHTML('beforeend', this.createRowFromTemplate());

        const createdRow = rowContainer.lastElementChild;
        this.updateRowFromAdder(adderDialog, createdRow);
        this.onlyDisplayRelevantFieldsForRow(createdRow);
    };

    MultiRowElementController.prototype.updateRowFromAdder = function(adderDialog, row)
    {
        this.extractFormFields(row)
            .forEach(function(formField, index) {
                this.setupFieldDisplaySync(formField);
                this.setFormFieldFromAdderDialog(adderDialog, index, formField);
            }.bind(this));
    };

    /**
     * Placeholder to handle removal of any rows from the form
     */
    MultiRowElementController.prototype.onRowRemoved = function()
    {};

    exports.MultiRow = MultiRowElementController;

})(OpenEyes.UI.ElementController, OpenEyes.Util);