(function (exports, AdderDialog) {

    function ElementController(options) {
        this.options = $.extend(true, {}, ElementController._defaultOptions, options);

        if (this.options.container === undefined) {
            console.log('controller requires container to initialise picker');
            return;
        }

        this.initialiseFormDisplay();

        this.setupAdder(this.defineAdderItemSets());

        this.listenForElementRemoval();
    }

    ElementController._defaultOptions = {
        container: undefined,
        nothing_selected_text: "-",
        letAdderDialogHandleOpenButton: true,
        adderDialogOptions: {
            deselectOnReturn: false,
        }
    };

    /**
     * Parse an element to retrieve all the form fields relevant for the adder dialog
     *
     * @param formContainer
     * @return {S[] | *[]}
     */
    ElementController.prototype.extractFormFields = function(formContainer)
    {
        return [].filter.call(
            formContainer.querySelectorAll('select, input'),
            function (formField) {
                return (formField.getAttribute('type') === undefined || formField.getAttribute('type') !== 'hidden')
                    && (formField.dataset.adderIgnore === undefined || formField.dataset.adderIgnore === false);
            });
    };

    /**
     * Mask all the form fields that should be display only and driven by the entered values
     *
     * @param formContainer
     */
    ElementController.prototype.initialiseFormDisplay = function(formContainer)
    {
        if (formContainer === undefined) {
            formContainer = this.options.container;
        }

        this.extractFormFields(formContainer)
            .forEach(function(formField) {
                this.setupFieldDisplaySync(formField);
            }.bind(this));
    };

    ElementController.prototype.defineAdderItemSets = function(formContainer)
    {
        if (formContainer === undefined) {
            formContainer = this.options.container;
        }

        return this.extractFormFields(formContainer)
            .map(function (formField) {
                return OpenEyes.UI.AdderDialog.Util.itemSetFromFormField(formField);
            });
    };

    /**
     * Abstraction of showing the selected value in the formField select as text in the given
     * displayDiv
     *
     * @param formField
     * @param displayDiv
     * @private
     */
    ElementController.prototype._updateDropdownDisplay = function(formField, displayDiv)
    {
        const selectedValues = formField.selectedOptions ?
            [].filter.call(formField.selectedOptions, function(optionElement) {
                return optionElement.value !== '' && optionElement.value !== null && optionElement.value !== undefined;
            }) : [];
        if (selectedValues.length === 0) {
            displayDiv.textContent = this.options.nothing_selected_text;
        }

        displayDiv.textContent = selectedValues.map(
            function(optionElement) {
                return optionElement.textContent;
            })
            .join(", ");
    };

    /**
     * Update the display div contents for the provided field based on its
     * current value
     *
     * @param field
     * @private
     */
    ElementController.prototype._updateFieldDisplay = function(field)
    {
        if (field.dataset.ecKeepField) {
            // we've not masked the field, so no display div to update
            return;
        }
        if (field.nodeName === 'SELECT') {
            this._updateDropdownDisplay(field, field.previousElementSibling);
        } else {
            field.previousElementSibling.textContent = field.value ? field.value : this.options.nothing_selected_text;
        }
    };

    /**
     * Set up the form field to be hidden and just display the selected value text
     *
     * @param field
     * @private
     */
    ElementController.prototype._initialiseFieldDisplay = function(field)
    {
        if (field.dataset.ecKeepField) {
            return;
        }
        let displayDiv = document.createElement("div");
        displayDiv.style.display = "inline-block";
        field.parentNode.insertBefore(displayDiv, field);
        this.toggleDomElement(field, false);
        this._updateFieldDisplay(field);
    };

    ElementController.prototype.setupFieldDisplaySync = function(formField)
    {
        this._initialiseFieldDisplay(formField);
        formField.addEventListener('change', this._updateFieldDisplay.bind(this, formField));
    };

    /**
     * Set up the defined adder form fields to display and sync with the adder dialog selections
     *
     * @private
     */
    ElementController.prototype._initialiseItemSetFieldDisplay = function()
    {
        this.adderFormFields.forEach(function(formField) {
            this._initialiseFieldDisplay(formField.field);
            formField.field.addEventListener('change', this._updateFieldDisplay.bind(this, formField.field));
        }.bind(this));

    };

    ElementController.prototype.getFormFieldFormattingSpec = function(formField)
    {
        let formatSpec = {};

        [].filter
            .call(Object.keys(formField.dataset), datasetKey => datasetKey.indexOf('ecFormat') > -1 )
            .forEach(datasetKey => formatSpec[datasetKey] = formField.dataset[datasetKey]);

        return formatSpec;
    }

    /**
     * looks for data attribute keys on the formField to format a value for the field if set.
     *
     * otherwise ensures value is a string.
     *
     * @param formatting
     * @param value
     * @return {string}
     */
    ElementController.prototype.formatValue = function(formatting, value)
    {
        let result = String(value);
        if (Object.keys(formatting).length === 0) {
            return result;
        }

        let prefix = '';

        const flt = parseFloat(value);
        if (!isNaN(flt)) {
            if (formatting.ecFormatForceSign && flt >= 0) {
                prefix = '+';
            }
            if (formatting.ecFormatFixed) {
                result = flt.toFixed(parseInt(formatting.ecFormatFixed));
            }
        }

        return prefix + result;
    };

    ElementController.prototype.getFormattedItemSetValueFromAdderDialog = function(adderDialog, itemSet, formatting)
    {
        let valueList = adderDialog.getSelectedItemsForItemSet(itemSet);
        if (valueList === undefined) {
            valueList = [];
        }
        if (!Array.isArray(valueList)) {
            valueList = [valueList];
        }

        return [].map.call(valueList, function(val) { return this.formatValue(formatting, val); }.bind(this));
    };

    /**
     *
     * @param OpenEyes.UI.AdderDialog adderDialog
     * @param int itemSetIndex
     * @param formField
     * @private
     */
    ElementController.prototype.setFormFieldFromAdderDialog = function(adderDialog, itemSetIndex, formField)
    {
        const value = this.getFormattedItemSetValueFromAdderDialog(
            adderDialog,
            adderDialog.options.itemSets[itemSetIndex],
            this.getFormFieldFormattingSpec(formField));

        if (formField.nodeName === 'SELECT') {
            [].forEach.call(formField.querySelectorAll('option'),
                function (optionElement) {
                    optionElement.selected = value !== null && value.includes(optionElement.value);
                });
        } else {
            formField.value = value.join(", ");
        }

        formField.dispatchEvent(new Event('change'));
    };

    /**
     * Hide or show the given dom element
     *
     * @param elementToToggle
     * @param showValue
     */
    ElementController.prototype.toggleDomElement = function(elementToToggle, showValue)
    {
        if (showValue) {
            elementToToggle.style.display = '';
        } else {
            elementToToggle.style.display = 'none';
        }
    };

    /**
     * Show or hide the given form field value display
     *
     * @param formField
     * @param showValue
     */
    ElementController.prototype.toggleFormField = function(formField, showValue)
    {
        // if element controller is not masking the input field
        // then we are toggling the actual field. Otherwise we are
        // toggling the display div.
        let elementToToggle = formField.dataset.ecKeepField ? formField : formField.previousElementSibling;

        this.toggleDomElement(elementToToggle, showValue);
    };


    /**
     * When we have fields that are dependent on the values of others, we need to
     * determine if they should be displayed or not.
     *
     * @param formContainer
     */
    ElementController.prototype.onlyDisplayRelevantFieldsForRow = function(formContainer)
    {
        formContainer.querySelectorAll('[data-adder-requires-item-set]')
            .forEach(function (formField) {
                let showValue;
                const dependentValue = formContainer.querySelector('[data-adder-id="' + formField.dataset.adderRequiresItemSet + '"]').value;

                if (!dependentValue) {
                    showValue = false;
                } else {
                    showValue = !formField.dataset.adderRequiresItemSetValues
                        || formField.dataset.adderRequiresItemSetValues.includes(dependentValue);
                }

                this.toggleFormField(formField, showValue);

            }.bind(this));
    };

    /**
     * placeholder function to allow child controllers to do pre-opening actions
     * for adder dialog
     *
     * @param adderDialog
     */
    ElementController.prototype.onAdderOpen = function(adderDialog)
    {};

    /**
     * placeholder function to allow child controllers to do post-closing actions
     * for adder dialog
     *
     * @param adderDialog
     */
    ElementController.prototype.onAdderClose = function(adderDialog)
    {};

    /**
     * Process the selected values from the adder dialog into the form elements.
     *
     * @param adderDialog
     * @param selectedItems
     */
    ElementController.prototype.updateFromAdder = function(adderDialog, selectedItems)
    {
        this.extractFormFields(this.options.container).forEach(function(formField, index) {
            this.setFormFieldFromAdderDialog(adderDialog, index, formField);
        }.bind(this));

        this.onlyDisplayRelevantFieldsForRow(this.options.container);
    };

    ElementController.prototype.getItemSetColFromAdder = function(itemSetId)
    {
        return this.adder.popup[0].querySelector('td[data-adder-id="' + itemSetId + '"]');
    };

    ElementController.prototype.hideOrShowListFromParent = function(parent, itemSet)
    {
        if (!itemSet.options.id) {
            console.error('dependent columns require an id option. use data-adder-id property on field definitions.');
        }

        const selected = parent.getElementsByClassName('selected');
        let showItemSet;

        if (!itemSet.options.requiresItemSetValues) {
            showItemSet = selected.length > 0;
        } else {
            showItemSet = selected && selected.length
                && [].filter.call(selected, function(selectedOption) {
                    return itemSet.options.requiresItemSetValues.includes(selectedOption.dataset.id);
                }).length > 0;
        }

        this.toggleItemSet(itemSet, showItemSet);
    };

    ElementController.prototype.toggleItemSet = function(itemSet, showItemSet)
    {
        const itemSetCol = this.getItemSetColFromAdder(itemSet.options.id);
        const itemSetColHeader = this.adder.popup[0].querySelector('th[data-id="' + itemSet.options.id + '"]');

        if (showItemSet) {
            if (itemSetCol.classList.contains('hidden')) {
                itemSetCol.classList.remove('hidden');
                itemSetColHeader.classList.remove('hidden');
                // check for default value to show
                const visibleDefaultOptions = Array.prototype.filter.call(
                    itemSetCol.querySelectorAll('li[data-set-default="true"]'),
                    li => li.style.display !== 'none');
                if (visibleDefaultOptions.length > 0) {
                    visibleDefaultOptions[0].scrollIntoView();
                }
            }
        } else {
            itemSetCol.classList.add('hidden');
            itemSetColHeader.classList.add('hidden');
        }
    }

    ElementController.prototype.hideOrShowListFromParentClick = function(event, itemSet)
    {
        this.hideOrShowListFromParent(event.target.closest('ul'), itemSet);
    };

    ElementController.prototype.getAdderOpenButton = function()
    {
        return this.options.container.querySelector('[data-adder-trigger="true"]');
    };

    /**
     * Create the Adder Dialog for this element form
     */
    ElementController.prototype.setupAdder = function(itemSets) {
        if (!this.adder) {
            this.adder = new AdderDialog(
                $.extend(true, this.options.adderDialogOptions, {
                    onOpen: this.onAdderOpen.bind(this),
                    onClose: this.onAdderClose.bind(this),
                    onReturn: this.updateFromAdder.bind(this),
                    itemSets: itemSets,
                    openButton: this.options.letAdderDialogHandleOpenButton ? $(this.getAdderOpenButton()) : null,
                    popupAnchor: this.options.letAdderDialogHandleOpenButton ? null : $(this.getAdderOpenButton())
                }));

            // set up dependency triggers
            itemSets.forEach(function(itemSet) {
                if (itemSet.options.requiresItemSet) {
                    const parentList = this.adder.popup[0]
                        .querySelector('ul[data-id="' + itemSet.options.requiresItemSet + '"]');

                    parentList.addEventListener('adder-change', function(event) {
                        this.hideOrShowListFromParentClick(event, itemSet);
                    }.bind(this));

                    // may be better with a single handler but this saves parsing out the different lists again
                    this.adder.popup[0].addEventListener('adder-reset', function() {
                        this.hideOrShowListFromParent(parentList, itemSet);
                    }.bind(this));

                    this.hideOrShowListFromParent(parentList, itemSet);
                }
            }.bind(this));
        }
    };

    ElementController.prototype.handleElementRemoval = function()
    {}; // placeholder

    ElementController.prototype.listenForElementRemoval = function()
    {
        const elementTypeClass = this.getElementTypeClass();

        if (!elementTypeClass) {
            return;
        }

        // because core event code is still based on jquery, we have to use jquery here
        $('.js-active-elements').on(
            'ElementRemoved',
            (event, removedElementClass) => {
                if (removedElementClass === elementTypeClass) {
                    this.handleElementRemoval();
                }
            }
        );
    };

    ElementController.prototype.getElementTypeClass = function()
    {
        const section = this.options.container.closest('section');
        if (section !== undefined) {
            return section.dataset.elementTypeClass;
        }
    };

    exports.ElementController = ElementController;
}(OpenEyes.UI, OpenEyes.UI.AdderDialog));