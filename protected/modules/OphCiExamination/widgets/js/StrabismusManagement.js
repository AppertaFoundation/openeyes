var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};
OpenEyes.Util = OpenEyes.Util || {};
OpenEyes.UI = OpenEyes.UI || {};

(function (exports, Util, UI) {

    const BaseController = UI.ElementController.MultiRow;

    function StrabismusManagement(options) {
        options = $.extend(true, {}, StrabismusManagement._defaultOptions, options);

        BaseController.call(this, options);
    }

    Util.inherits(BaseController, StrabismusManagement);

    StrabismusManagement._defaultOptions = {
        treatments: [],
        treatmentReasons: [],
        nothing_selected_text: '',
        adderDialogOptions: {
            listFilter: true,
            filterListId: 'treatment',
            listForFilterId: 'value',
        },
    };

    /**
     * Custom adder sets because we are driving them from provided options rather than
     * form fields.
     *
     * @param formContainer
     * @return {[ItemSet, ItemSet, ItemSet, ItemSet, ItemSet]}
     */
    StrabismusManagement.prototype.defineAdderItemSets = function(formContainer)
    {
        return [
            new UI.AdderDialog.ItemSet(this.getItemSetItemsForTreatments(), {
                id: 'treatment',
                mandatory: true
            }),
            new UI.AdderDialog.ItemSet(this.getItemSetItemsForTreatmentOptions(0), {
                id: 'column-1',
                requiresItemSet: 'treatment'
            }),
            new UI.AdderDialog.ItemSet(this.getItemSetItemsForTreatmentOptions(1), {
                id: 'column-2',
                requiresItemSet: 'treatment'
            }),
            new UI.AdderDialog.ItemSet(this.getItemSetItemsForTreatmentReasons(), {
                id: 'treatment-reason'
            }),
            new UI.AdderDialog.ItemSet(this.getItemSetItemsForSide(), {
                id: 'side',
                multiSelect: true
            })
        ];
    };

    /**
     * Complete override here because we are combining adder dialog selections into
     * a single column, and setting up the laterality display
     *
     * @param adderDialog
     * @param row
     */
    StrabismusManagement.prototype.updateRowFromAdder = function(adderDialog, row)
    {
        this.setTreatmentFieldValue(adderDialog, row.querySelector('.js-treatment'));
        this.setTreatmentOptionsFieldValue(adderDialog, row.querySelector('.js-treatment-options'));
        this.setTreatmentReasonFieldValue(adderDialog, row.querySelector('.js-treatment-reason'));
        this.updateRowEyeFromAdder(adderDialog, row);
    };

    StrabismusManagement.prototype.setTreatmentFieldValue = function(adderDialog, formField)
    {
        this.setupFieldDisplaySync(formField);
        const val = this.getFormattedItemSetValueFromAdderDialog(
            adderDialog,
            adderDialog.options.itemSets[0],
            {});

        formField.value = val.length
            ? this.options.treatments.filter(treatment => treatment.id === val[0])[0].value
            : '';
        formField.dispatchEvent(new Event('change'));
    };

    StrabismusManagement.prototype.setTreatmentOptionsFieldValue = function(adderDialog, formField)
    {
        this.setupFieldDisplaySync(formField);
        formField.value = [
            this.getTreatmentOptionLabels(
                this.getFormattedItemSetValueFromAdderDialog(
                    adderDialog,
                    adderDialog.options.itemSets[1],
                    {})
            ).join(", "),
            this.getTreatmentOptionLabels(
                this.getFormattedItemSetValueFromAdderDialog(
                adderDialog,
                adderDialog.options.itemSets[2],
                {})
            ).join(", ")
        ].join(" ");
        formField.dispatchEvent(new Event('change'));
    };

    StrabismusManagement.prototype.setTreatmentReasonFieldValue = function(adderDialog, formField)
    {
        this.setupFieldDisplaySync(formField);
        const val = this.getFormattedItemSetValueFromAdderDialog(
            adderDialog,
            adderDialog.options.itemSets[3],
            {});

        formField.value = val.length
            ? this.options.treatmentReasons.filter(reason => reason.id === val[0])[0].value
            : '';
        this.toggleDomElement(formField.closest('td').querySelector('.js-none-display'), formField.value === '');
        formField.dispatchEvent(new Event('change'));
    };

    StrabismusManagement.prototype.updateRowEyeFromAdder = function(adderDialog, row)
    {
        const eyeField = row.querySelector('.js-eye');
        eyeField.value = this.getFormattedItemSetValueFromAdderDialog(
            adderDialog,
            adderDialog.options.itemSets[4],
            {})
            .reduce((calculatedEyeValue, eyeVal) => {
                return exports.addEyeToEyeValue(calculatedEyeValue, eyeVal, false);
            }, 0);

        Object.keys(exports.eyeValues)
            .forEach(eyeLabel => {
                if (exports.eyeValueHasEye(eyeField.value, eyeLabel)) {
                    row.querySelector('.js-' + eyeLabel + '-laterality').classList.remove('NA');
                } else {
                    row.querySelector('.js-' + eyeLabel + '-laterality').classList.add('NA');
                }
            });
    };

    StrabismusManagement.prototype.hideOrShowListFromParent = function(parent, itemSet)
    {
        StrabismusManagement._super.prototype.hideOrShowListFromParent.call(this, parent, itemSet);

        if (['column-1', 'column-2'].includes(itemSet.options.id)) {
            const selected = parent.getElementsByClassName('selected');
            if (!selected.length) {
                // should be hidden already
                return;
            }
            const selectedId = selected[0].dataset.id;
            const itemSetCol = this.getItemSetColFromAdder(itemSet.options.id);

            // any items that don't match the selectedId should be de-selected
            Array.prototype.forEach.call(
                itemSetCol.querySelectorAll('li:not([data-filter_id="' + selectedId + '"])'),
                item => item.classList.remove('selected')
            );

            // if no relevant items hide the whole column
            if (Array.prototype.filter.call(
                    itemSetCol.querySelectorAll('li'),
                    item => item.dataset.filter_id === selectedId).length === 0
            ) {
                this.toggleItemSet(itemSet, false);
                return;
            }

            // otherwise just display relevant
            itemSetCol.querySelectorAll('li')
                .forEach(item => {
                    this.toggleDomElement(item, item.dataset.filter_id === selectedId);
                });

            // set multiselect property on column based on the treatment spec
            const columnDefinitions = this.options.treatments
                .filter(treatment => treatment.id === selectedId)[0]['columns'];
            const isMultiSelect = itemSet.options.id === 'column-1'
                ? columnDefinitions[0].multiselect
                : columnDefinitions[1].multiselect;
            const itemSetColList = itemSetCol.querySelector('ul');
            if (isMultiSelect) {
                if (!itemSetColList.classList.contains('multi')) {
                    itemSetColList.classList.add('multi');
                }
            } else {
                itemSetColList.classList.remove('multi');
            }
            itemSetColList.dataset.multiselect = isMultiSelect;
            // // adder dialog is relying on jquery, so fall back to it here
            // $(itemSetColList).data('multiselect', isMultiSelect);
        }
    };

    StrabismusManagement.prototype.getItemSetItemsForTreatments = function()
    {
        return this.options.treatments.map(treatment => {
            return {
                label: treatment.value,
                id: treatment.id,
                filter_value: treatment.id
            };
        });
    };

    StrabismusManagement.prototype.getItemSetItemsForTreatmentOptions = function(columnIndex)
    {
        return this.options.treatments
            .filter(treatment => treatment.columns !== undefined && treatment.columns[columnIndex] !== undefined)
            .flatMap(treatment =>  {
                return treatment.columns[columnIndex].options.map(option => {
                    return {
                        label: option.value,
                        id: option.id,
                        filter_id: treatment.id
                    };
                });
            });
    };

    /**
     * we use the eye string as the id as well as for the label
     * id value parsing handled by form field value setting
     *
     * @return {{id: string, label: string}[]}
     */
    StrabismusManagement.prototype.getItemSetItemsForSide = function()
    {
        return Object.keys(exports.eyeValues).map(k => {
            return {
                id: k,
                label: k
            };
        });
    };

    StrabismusManagement.prototype.getItemSetItemsForTreatmentReasons = function()
    {
        return this.options.treatmentReasons
            .map(reason =>  {
                return {
                    label: reason.value,
                    id: reason.id
                };
            });
    };

    StrabismusManagement.prototype.getTreatmentOptionLabels = function(selectedIds)
    {
        if (!this.treatmentOptionsById) {
            this.treatmentOptionsById = this.options.treatments
                .flatMap(treatment => treatment.columns)
                .flatMap(column => column.options)
                .reduce((obj, option) => {
                    obj[option.id] = option;
                    return obj;
                }, {});
        }
        return selectedIds.map(id => this.treatmentOptionsById[id].value);
    };

    exports.StrabismusManagementController = StrabismusManagement;
})(OpenEyes.OphCiExamination, OpenEyes.Util, OpenEyes.UI);
