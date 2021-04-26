var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};
OpenEyes.Util = OpenEyes.Util || {};
OpenEyes.UI = OpenEyes.UI || {};

(function (exports, Util, UI) {

    const BaseController = UI.ElementController.MultiRow;

    /**
     * Because there is a large number of options for units etc, they are provided
     * to this controller as configuration, rather than parsing the form component
     * for them.
     *
     * @param options
     * @constructor
     */
    function VisualAcuity(options) {
        options = $.extend(true, {}, VisualAcuity._defaultOptions, options);
        options.cannotRecordSelector = '.' + options.cannotRecordClass;
        options.cannotAssessSelector = '.' + options.cannotAssessClass;

        BaseController.call(this, options);

        this.updateReadingRowsDisplay();

        this.initialiseTriggers();

        this.updateFormBasedOnCannotAssessSelections();
    }

    Util.inherits(BaseController, VisualAcuity);

    VisualAcuity._defaultOptions = {
        rowTemplate: undefined, // DOM element containing the row template
        readingsFormSelector: '.readings',
        noReadingsFormSelector: '.no-readings',
        cannotRecordClass: 'js-cannot-record',
        cannotAssessClass: 'js-cannot-assess',
        vaUnitOptions: undefined, // this is the data structure to pass the units in
        adderDialogOptions: {
            listFilter: true,
            filterListId: 'unit_id',
            listForFilterId: 'value',
        },
        trackCvi: false
    };

    VisualAcuity.prototype.initialiseTriggers = function()
    {
        const formContainer = this.options.container;

        const removeLink = formContainer.querySelector('.remove-side');
        if (removeLink) {
            removeLink.addEventListener('click', this.removeSide.bind(this));
        }

        const addLink = formContainer.querySelector('.inactive-form a');
        if (addLink) {
            addLink.addEventListener('click', this.addSide.bind(this));
        }

        this.getCannotRecordInputs().forEach(function(formInput) {
            formInput.addEventListener('click', this.updateFormBasedOnCannotAssessSelections.bind(this));
        }.bind(this));
    };

    /**
     * Hides the form for the given side, and then determines whether we need to force show
     * a side (we cannot have no sides showing in the form).
     *
     * Finally updates the form field with the correct side value based
     * on the forms that are active.
     *
     * @param removedSide
     */
    VisualAcuity.prototype.updateEyeAndDisplay = function(removedSide)
    {
        let elementContainer = this.options.container.closest('section');
        let activeForms = [].filter.call(
            elementContainer.querySelectorAll('.js-element-eye .active-form'),
            function(form) {
                return form.style.display !== 'none';
            });

        if (activeForms.length === 0) {
            const forceDisplaySide = removedSide === 'right' ? '.js-element-eye.left-eye' : '.js-element-eye.right-eye';
            const forceSide = elementContainer.querySelector(forceDisplaySide);
            const forceActive = forceSide.querySelector('.active-form');

            this.toggleDomElement(forceActive, true);
            this.toggleDomElement(forceSide.querySelector('.inactive-form'), false);

            activeForms = [forceActive];
        }

        let calculatedEye = 0;
        [].forEach.call(activeForms,
            function(form) {
                const formSide = form.closest('.js-element-eye').dataset.side;
                calculatedEye = exports.addEyeToEyeValue(calculatedEye, formSide, true);
            });

        elementContainer.querySelector('input.sideField').value = calculatedEye;
    };

    VisualAcuity.prototype.removeSide = function(event)
    {
        event.stopPropagation(); // prevent generic behaviour occurring
        let container = event.target.closest('.js-element-eye');
        this.toggleDomElement(container.querySelector('.active-form'), false);
        this.toggleDomElement(container.querySelector('.inactive-form'), true);
        this.updateEyeAndDisplay(container.dataset.side);
    };

    VisualAcuity.prototype.addSide = function(event)
    {
        event.stopPropagation(); // prevent generic behaviour occurring
        let container = event.target.closest('.js-element-eye');
        this.toggleDomElement(container.querySelector('.active-form'), true);
        this.toggleDomElement(container.querySelector('.inactive-form'), false);
        this.updateEyeAndDisplay();
    };

    /**
     * The fields for unit and value contain the appropriate adderDialog config,
     * but the options are defined by the controller configuration instead.
     *
     * So here we override to inject the appropriate values into those itemsets
     *
     * @param formContainer
     * @return {*}
     */
    VisualAcuity.prototype.defineAdderItemSets = function(formContainer)
    {
        let parsedItemSets = VisualAcuity._super.prototype.defineAdderItemSets.call(this, formContainer);
        parsedItemSets[1] = new UI.AdderDialog.ItemSet(this.getItemSetItemsForUnits(), parsedItemSets[1].options);
        parsedItemSets[2] = new UI.AdderDialog.ItemSet(this.getItemSetItemsForUnitValues(), parsedItemSets[2].options);
        return parsedItemSets;
    };

    /**
     * Generate the items to be used in an ItemSet for VA Units
     *
     * @return {*}
     */
    VisualAcuity.prototype.getItemSetItemsForUnits = function()
    {
        if (this.options.vaUnitOptions === undefined) {
            console.error('ERROR: vaUnitOptions must be set on VisualAcuity');
        }

        return this.options.vaUnitOptions.map(function(vaUnit) {
            return {
                label: vaUnit.name,
                id: vaUnit.id,
                filter_value: vaUnit.id
            };
        });
    };

    /**
     * Generate the items to be used in an ItemSet for VA Unit Values
     *
     * @return {Array|*|any[]}
     */
    VisualAcuity.prototype.getItemSetItemsForUnitValues = function()
    {
        if (this.options.vaUnitOptions === undefined) {
            console.error('ERROR: vaUnitOptions must be set on VisualAcuity');
        }

        return this.options.vaUnitOptions.flatMap(function(vaUnit) {
            return vaUnit.values.map(function(vaUnitValue) {
                return {
                    label: vaUnitValue.value,
                    id: vaUnitValue.base_value,
                    filter_value: vaUnit.id,
                    'set-default': vaUnitValue.default
                };
            });
        });
    };

    /**
     * Override for unit and value display to map to the defined values that are
     * available for the units
     *
     * @param field
     * @return {undefined}
     * @private
     */
    VisualAcuity.prototype._updateFieldDisplay = function(field)
    {
        if (!field.dataset.acuityField) {
            return VisualAcuity._super.prototype._updateFieldDisplay.call(this, field);
        }

        if (field.dataset.acuityField === 'unit') {
            field.previousElementSibling.textContent = this.getLabelForVAUnitIdFromField(field);
        }

        if (field.dataset.acuityField === 'value') {
            field.previousElementSibling.textContent = this.getLabelforVAValueFromField(field);
        }
    };

    /**
     * Show or hide the readings table and the checkboxes for missing eyes etc
     */
    VisualAcuity.prototype.updateReadingRowsDisplay = function()
    {
        const readingsContainer = this.options.container.querySelector(this.options.readingsFormSelector);
        const noReadingsFormContainer = this.options.container.querySelector(this.options.noReadingsFormSelector);

        if (readingsContainer.querySelectorAll(this.options.rowsSelector).length > 0) {
            this.toggleDomElement(readingsContainer, true);
            if (noReadingsFormContainer) {
                this.toggleDomElement(this.options.container.querySelector(this.options.noReadingsFormSelector), false);
            }
        } else {
            this.toggleDomElement(readingsContainer, false);
            if (noReadingsFormContainer) {
                this.toggleDomElement(this.options.container.querySelector(this.options.noReadingsFormSelector), true);
            }
        }
    };

    VisualAcuity.prototype.getCannotRecordInputs = function()
    {
        if (!this._cannotRecordInputs) {
            this._cannotRecordInputs = this.options.container
                .querySelectorAll(this.options.cannotRecordSelector);
        }
        return this._cannotRecordInputs;
    };

    VisualAcuity.prototype.getCannotAssessInputs = function()
    {
        if (!this._cannotAssessInputs) {
            this._cannotAssessInputs = this.options.container
                .querySelectorAll(this.options.cannotAssessSelector);
        }
        return this._cannotAssessInputs;
    };

    VisualAcuity.prototype.getCanAssessButNotRecordInputs = function()
    {
        if (!this._canAssessButNotRecordInputs) {
            this._canAssessButNotRecordInputs = [].filter.call(
                this.getCannotRecordInputs(),
                function (formElement) {
                    return !formElement.classList.contains(this.options.cannotAssessClass);
                });
        }
        return this._canAssessButNotRecordInputs;
    };

    VisualAcuity.prototype.updateFormBasedOnCannotAssessSelections = function()
    {
        const canRecord = [].filter.call(
            this.getCannotRecordInputs(),
            function(formElement) {
                return formElement.checked;
            }).length === 0;

        this.updateAdderButtonDisplay(canRecord);
        this.updateCannotRecordInputDisplay(canRecord);
    };

    VisualAcuity.prototype.updateCannotRecordInputDisplay = function(canRecord)
    {
        if (canRecord) {
            [].forEach.call(
                this.getCannotRecordInputs(),
                formElement => this.toggleDomElement(formElement.parentNode, true)
            );
        } else {
            const canAssess = [].filter.call(
                this.getCannotAssessInputs(),
                function(formElement) {
                    return formElement.checked;
                }).length > 0;
            [].forEach.call(
                this.getCannotRecordInputs(),
                formElement => this.toggleDomElement(
                    formElement.parentNode,
                    canAssess ?
                        formElement.classList.contains(this.options.cannotAssessClass) :
                        !formElement.classList.contains(this.options.cannotAssessClass)
                )
            );
        }
    };

    /**
     * Determine if we can add a reading or not, and show/hide the
     * add button accordingly.
     */
    VisualAcuity.prototype.updateAdderButtonDisplay = function(canRecord)
    {
        this.toggleDomElement(this.getAdderOpenButton(), canRecord);
    };

    VisualAcuity.prototype.updateFromAdder = function(adderDialog, selectedItems)
    {
        VisualAcuity._super.prototype.updateFromAdder.call(this, adderDialog, selectedItems);
        this.updateReadingRowsDisplay();
        this.updateForCvi();
    };

    VisualAcuity.prototype.onRowRemoved = function()
    {
        this.updateReadingRowsDisplay();
        this.updateForCvi();
    };

    VisualAcuity.prototype.getVAUnitDataFromField = function(field)
    {
        if (field.value === undefined || field.value.length === 0) {
            return undefined;
        }

        return this.options.vaUnitOptions.filter(function(vaUnit) {
            return vaUnit.id === field.value;
        })[0];
    };

    VisualAcuity.prototype.getLabelForVAUnitIdFromField = function(field)
    {
        const unitData = this.getVAUnitDataFromField(field);
        return unitData !== undefined ? unitData.name : this.options.nothing_selected_text;
    };

    VisualAcuity.prototype.getLabelforVAValueFromField = function(field)
    {
        const rowUnitField = field.closest('tr').querySelector('[data-acuity-field="unit"]');
        const unitData = this.getVAUnitDataFromField(rowUnitField);

        if (unitData === undefined) {
            return this.options.nothing_selected_text;
        }

        const unitValueData = unitData.values.filter(function(valueData) {
            return valueData.base_value === field.value;
        })[0];

        return unitValueData !== undefined ? unitValueData.value : this.options.nothing_selected_text;
    };

    VisualAcuity.prototype.updateForCvi = function()
    {
        if (this.options.trackCvi) {
            // this global function is provided by the core module VisualAcuity javascript
            updateCviAlertState(this.options.container.closest('section'));
        }
    };

    exports.VisualAcuityController = VisualAcuity;
}(OpenEyes.OphCiExamination, OpenEyes.Util, OpenEyes.UI));
