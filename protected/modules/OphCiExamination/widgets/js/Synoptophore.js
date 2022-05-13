var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};
OpenEyes.Util = OpenEyes.Util || {};
OpenEyes.UI = OpenEyes.UI || {};

(function (exports, Util, UI) {
    const BaseController = UI.ElementController;

    /*
     * This JS is a variation from standard AdderDialog integration.
     * AdderDialog is created by code rather than automatically from widget edit view
     */

    function Synoptophore(options) {

        this.options = $.extend(true, {}, Synoptophore._defaultOptions, options);

        // Deliberately not calling Parent init

        this.setupAdders();
    }

    Util.inherits(BaseController, Synoptophore);

    Synoptophore._defaultOptions = {
        container: undefined,
        directionOptions: [],
        deviationOptions: [],
        headers: [],
        adderDialogOptions: {
            deselectOnReturn: true
        }
    };

    /**
     * @returns {*[]}
     */
    Synoptophore.prototype.getAdderItemSets = function () {
        if (this.itemSets === undefined) {
            this.itemSets = [
                new UI.AdderDialog.ItemSet([],
                    {
                        header: this.options.headers["horizontal_angle"],
                        supportSigns: true,
                        generateFloatNumberColumns: {
                            decimalPlaces: 0,
                            minValue: -60,
                            maxValue: 60
                        },
                        id: "horizontal_angle"
                    }),
                new UI.AdderDialog.ItemSet([],
                    {
                        header: this.options.headers["vertical_power"],
                        generateFloatNumberColumns: {
                            decimalPlaces: 0,
                            minValue: 0,
                            maxValue: 50
                        },
                        id: "vertical_power"
                    }),
                new UI.AdderDialog.ItemSet(
                    this.options.directionOptions,
                    {
                        header: this.options.headers["direction"],
                        id: "direction"
                    }),
                new UI.AdderDialog.ItemSet([],
                    {
                        header: this.options.headers["torsion"],
                        generateFloatNumberColumns: {
                            decimalPlaces: 0,
                            minValue: 0,
                            maxValue: 40
                        },
                        id: "torsion"
                    }),
                new UI.AdderDialog.ItemSet(
                    this.options.deviationOptions,
                    {
                        id: "deviation"
                    }
                ),
            ];

            this.allFieldIds = this.itemSets.map(itemSet => itemSet.options.id);
        }


        return this.itemSets;
    };

    Synoptophore.prototype.getItemSetForId = function (adderDialog, itemSetId) {
        return [].filter.call(adderDialog.options.itemSets, function (itemSet) {
            return itemSet.options.id === itemSetId;
        })[0];
    };

    Synoptophore.prototype.getSelectedValueFromAdderDialog = function (adderDialog, itemSetId) {
        return this.getFormattedItemSetValueFromAdderDialog(adderDialog,
            this.getItemSetForId(adderDialog, itemSetId),
            {});
    };

    Synoptophore.prototype.updateFromAdder = function (gazeType, adderDialog) {
        let selectedValues = {};
        ['horizontal_angle', 'vertical_power', 'direction', 'torsion', 'deviation']
            .forEach(function (itemSetId) {
                selectedValues[itemSetId] = this.getSelectedValueFromAdderDialog(adderDialog, itemSetId);
            }.bind(this));

        this.assignAdderValuesToForm(gazeType, selectedValues);
        this.assignAdderValuesToDisplay(gazeType, selectedValues);
        this.switchAdderButtonsForGazeType(gazeType, false);
    };

    Synoptophore.prototype.removeGazeTypeRecord = function (gazeType) {
        let gazeTypeContainer = this.getGazeTypeContainer(gazeType);

        ['horizontal_angle', 'vertical_power', 'direction', 'torsion', 'deviation'].forEach(function (itemSetId) {
            let valueInput = gazeTypeContainer.querySelector('[data-adder-input-id="' + itemSetId + '"]');
            valueInput.value = "";
            valueInput.setAttribute('disabled', 'disabled');
        });
        gazeTypeContainer.querySelector('[data-adder-gaze-type="true"]').setAttribute('disabled', 'disabled');
        gazeTypeContainer.querySelector('.data-value').innerHTML = "";

        this.switchAdderButtonsForGazeType(gazeType, true);
    };

    Synoptophore.prototype.assignAdderValuesToForm = function (gazeType, selectedValues) {
        let gazeTypeContainer = this.getGazeTypeContainer(gazeType);

        for (const [key, value] of Object.entries(selectedValues)) {
            let valueInput = gazeTypeContainer.querySelector('[data-adder-input-id="' + key + '"]');
            valueInput.value = value;
            valueInput.removeAttribute('disabled');
        }

        gazeTypeContainer.querySelector('[data-adder-gaze-type="true"]').removeAttribute('disabled');
    };

    Synoptophore.prototype.assignAdderValuesToDisplay = function (gazeType, selectedValues) {
        let gazeTypeContainer = this.getGazeTypeContainer(gazeType);

        let displayElements = [];
        if (selectedValues['horizontal_angle'].length) {
            displayElements.push("+" + selectedValues['horizontal_angle'] + "°");
        }
        if (selectedValues['vertical_power'].length) {
            displayElements.push(selectedValues['vertical_power'] + "Δ");
        }
        if (selectedValues['direction'].length) {
            displayElements.push(
                this.options.directionOptions[
                    this.options.directionOptions.findIndex(option => option.id === selectedValues['direction'].toString())
                ]['label']);
        }
        if (selectedValues['torsion'].length) {
            displayElements.push(selectedValues['torsion']);
        }
        if (selectedValues['deviation'].length) {
            displayElements.push(
                this.options.deviationOptions[
                    this.options.deviationOptions.findIndex(option => option.id === selectedValues['deviation'].toString())
                ]['abbreviation']
            );
        }
        gazeTypeContainer.querySelector('.data-value').innerHTML = displayElements.join(" ");
    };

    Synoptophore.prototype.switchAdderButtonsForGazeType = function (gazeType, showAdderButton) {
        let gazeTypeContainer = this.getGazeTypeContainer(gazeType);
        let gazeTypeAddButton = gazeTypeContainer.querySelector('[data-adder-trigger="true"]');
        let gazeTypeRemoveButton = gazeTypeContainer.querySelector('[data-remove-reading="true"]');

        if (showAdderButton) {
            this.toggleDomElement(gazeTypeAddButton, true);
            this.toggleDomElement(gazeTypeRemoveButton, false);
        } else {
            this.toggleDomElement(gazeTypeAddButton, false);
            this.toggleDomElement(gazeTypeRemoveButton, true);
        }
    };

    Synoptophore.prototype.getGazeTypeContainer = function (gazeType) {
        return this.options.container.querySelector('.gaze-container[data-gaze-type="' + gazeType + '"]');
    };

    Synoptophore.prototype.validateAdderDialogSelection = function (adderDialog)
    {
        const somethingSelected = this.allFieldIds
            .map(id => this.getSelectedValueFromAdderDialog(adderDialog, id))
            .filter(val => val.length > 0)
            .length > 0;

        if (somethingSelected) {
            return true;
        }

        new UI.Dialog.Alert({
            content: "At least one valid value must be selected."
        }).open();

        return false;
    };

    /**
     * Itemsets for AdderDialog are universal to prevent unnecessary repetition of elements     *
     * On save updateFromAdder method saves values to relevant Gaze Type form elements
     */
    Synoptophore.prototype.setupAdders = function () {
        this.options.container.querySelectorAll('[data-adder-trigger="true"]')
            .forEach(function (btn) {
                const gazeType = btn.dataset.gazeType;
                new UI.AdderDialog($.extend(true, this.options.adderDialogOptions, {
                    onOpen: function (adderDialog) {

                    }.bind(this),
                    onReturn: function (adderDialog) {
                        if (!this.validateAdderDialogSelection(adderDialog)) {
                            return false;
                        }
                        this.updateFromAdder(gazeType, adderDialog);
                    }.bind(this),
                    itemSets: this.getAdderItemSets(),
                    openButton: $(btn)
                }));
            }.bind(this));
        this.options.container.querySelectorAll('[data-remove-reading="true"]')
            .forEach(function (btn) {
                const gazeType = btn.dataset.gazeType;
                btn.addEventListener('click', function (event) {
                    event.preventDefault();
                    this.removeGazeTypeRecord(gazeType);

                }.bind(this));
            }.bind(this));
    };

    exports.SynoptophoreController = Synoptophore;
})(OpenEyes.OphCiExamination, OpenEyes.Util, OpenEyes.UI);