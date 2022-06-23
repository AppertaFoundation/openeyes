var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};
OpenEyes.Util = OpenEyes.Util || {};
OpenEyes.UI = OpenEyes.UI || {};

(function(exports, Util, UI) {

    function EyedrawController(options)
    {
        this.options = $.extend(true, {}, EyedrawController._defaultOptions, options);

        if (this.options.container === undefined) {
            console.error('container must be defined');
            return;
        }

        if (this.options.container.edController === undefined) {
            this.options.container.edController = this;
        }
        if (this.options.container.edController !== this) {
            console.error('cannot have multiple controllers on the same container');
        }

        this.initialise();
    }

    EyedrawController._defaultOptions = {
        'container': undefined,
        'uniquePatterns': [
            'XPattern', 'YPattern', 'InverseYPattern', 'VPattern', 'APattern'
        ]
    };

    /**
     * Simple method to keep track of the drawing canvases for the reading under control.
     *
     * @param _drawing
     */
    EyedrawController.prototype.initialiseDrawing = function(drawing)
    {
        let side = drawing.eye === 1 ? 'left' : 'right';
        if (!this[side + 'Drawing']) {
            this[side+'Drawing'] = drawing;
        }
    };


    /**
     * Set up doodle control button handling
     *
     */
    EyedrawController.prototype.initialise = function()
    {
        this.options.container
            .querySelectorAll('.ed-button')
            .forEach(btn => btn.addEventListener('click', this.handleButton.bind(this)));
    };

    /**
     * Work out what action to take based on the given click event.
     *
     * @param e
     */
    EyedrawController.prototype.handleButton = function(e)
    {
        let clicked = e.target;

        this.leftDrawing.deselectDoodles();
        this.rightDrawing.deselectDoodles();

        if (!clicked.matches('a')) {
            clicked = clicked.closest('a');
        }

        let fn = clicked.dataset['function'];
        let side = clicked.dataset.eye;
        if (side) {
            this[fn](clicked.dataset.arg, side);
        }
        else {
            this[fn](clicked.dataset.arg);
        }
    };

    /**
     * Function for handling the pattern doodles
     *
     * @param patternClass
     */
    EyedrawController.prototype.addPattern = function(patternClass)
    {
        this.options.uniquePatterns.forEach(pattern => {
            this.leftDrawing.deleteDoodlesOfClass(pattern);
            this.rightDrawing.deleteDoodlesOfClass(pattern);
        });

        if (patternClass) {
            this.leftDrawing.addDoodle(patternClass);
            this.rightDrawing.addDoodle(patternClass);
            // deselect to prevent control tray displaying
            this.leftDrawing.deselectDoodles();
            this.rightDrawing.deselectDoodles();
        }
    };

    /**
     *
     * @param doodleClass
     * @param side
     */
    EyedrawController.prototype.addDoodle = function(doodleClass, side)
    {
        var drawing = this[side+'Drawing'];
        if (drawing) {
            drawing.addDoodle(doodleClass);
        }
    };

    /**
     *
     * @param doodleClass
     * @param side
     */
    EyedrawController.prototype.shootOrDrift = function(doodleClass, side)
    {
        if (side === 'left') {
            let doodle = this.leftDrawing.addDoodle(doodleClass);
            doodle.setParameterFromString('originX', (-doodle.quadrantPoint.x).toString());
            doodle.setParameterFromString('originY', (-doodle.quadrantPoint.y).toString());
            this.leftDrawing.repaint();
        } else {
            let doodle = this.rightDrawing.addDoodle(doodleClass);
            doodle.setParameterFromString('originX', doodle.quadrantPoint.x.toString());
            doodle.setParameterFromString('originY', (-doodle.quadrantPoint.y).toString());
            this.leftDrawing.repaint();
        }
    };

    exports.NinePositionsEyedrawController = EyedrawController;

    function NinePositionsReadingController(options) {
        this.options = $.extend(true, {}, NinePositionsReadingController._defaultOptions, options);

        if (this.options.container === undefined) {
            console.error('must have a container to control');
        }

        this.horizontalDeviationIds = ['horizontal_prism_position', 'horizontal_e_deviation_id', 'horizontal_x_deviation_id'];
        this.verticalDeviationIds = ['vertical_prism_position', 'vertical_deviation_id'];
        this.allFieldIds = [].concat.apply(['horizontal_angle', 'vertical_angle'], [this.horizontalDeviationIds, this.verticalDeviationIds]);

        this.setupAdders();
    }

    Util.inherits(UI.ElementController, NinePositionsReadingController);

    NinePositionsReadingController._defaultOptions = {
        container: undefined,
        horizontalPrismPositionOptions: [],
        horizontalEDeviationOptions: [],
        horizontalXDeviationOptions: [],
        verticalPrismPositionOptions: [],
        verticalDeviationOptions: [],
        headers: {
            horizontal: 'Horizontal',
            vertical: 'Vertical'
        },
        adderDialogOptions: {
            deselectOnReturn: true
        }
    };

    NinePositionsReadingController.prototype.getAdderItemSets = function()
    {
        if (this.itemSets === undefined) {
            this.itemSets = [
                new UI.AdderDialog.ItemSet([],
                    {
                        mandatory: false,
                        header: this.options.headers["horizontal"],
                        generateFloatNumberColumns: {
                            decimalPlaces: 0,
                            minValue: 0,
                            maxValue: 90
                        },
                        id: "horizontal_angle"
                    }
                ),
                new UI.AdderDialog.ItemSet(
                    this.options.horizontalPrismPositionOptions,
                    {
                        mandatory: false,
                        id: "horizontal_prism_position"
                    }
                ),
                new UI.AdderDialog.ItemSet(
                    this.options.horizontalEDeviationOptions,
                    {
                        mandatory: false,
                        id: "horizontal_e_deviation_id"
                    }
                ),
                new UI.AdderDialog.ItemSet(
                    this.options.horizontalXDeviationOptions,
                    {
                        mandatory: false,
                        id: "horizontal_x_deviation_id"
                    }
                ),
                new UI.AdderDialog.ItemSet([],
                    {
                        mandatory: false,
                        header: this.options.headers["vertical"],
                        generateFloatNumberColumns: {
                            decimalPlaces: 0,
                            minValue: 0,
                            maxValue: 50
                        },
                        id: "vertical_angle"
                    }
                ),
                new UI.AdderDialog.ItemSet(
                    this.options.verticalPrismPositionOptions,
                    {
                        mandatory: false,
                        id: "vertical_prism_position"
                    }
                ),
                new UI.AdderDialog.ItemSet(
                    this.options.verticalDeviationOptions,
                    {
                        mandatory: false,
                        id: "vertical_deviation_id"
                    }
                ),
            ];
        }

        return this.itemSets;
    };

    NinePositionsReadingController.prototype.cleanDeviationSelectionForGazeType = function(gazeType, selectionIdToKeep)
    {

        this.cleanOtherSelections(this.adderDialogsByGazeType[gazeType],selectionIdToKeep, this.horizontalDeviationIds);

        this.cleanOtherSelections(this.adderDialogsByGazeType[gazeType], selectionIdToKeep, this.verticalDeviationIds);

    };

    NinePositionsReadingController.prototype.cleanOtherSelections = function(adderDialog, selectionIdToKeep, ids)
    {
        if (!ids.includes(selectionIdToKeep)) {
            return;
        }
        adderDialog.removeSelectedColumnById(ids.filter(id => id !== selectionIdToKeep));
    };

    NinePositionsReadingController.prototype.getItemSetForId = function (adderDialog, itemSetId)
    {
        return [].filter.call(adderDialog.options.itemSets, function (itemSet) {
            return itemSet.options.id === itemSetId;
        })[0];
    };

    NinePositionsReadingController.prototype.getSelectedValueFromAdderDialog = function (adderDialog, itemSetId)
    {
        return this.getFormattedItemSetValueFromAdderDialog(adderDialog,
            this.getItemSetForId(adderDialog, itemSetId),
            {});
    };

    /**
     * Returns true if the adderDialog selection is valid
     *
     * @param adderDialog
     * @return {boolean}
     */
    NinePositionsReadingController.prototype.validateAdderDialogSelection = function (adderDialog)
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

    NinePositionsReadingController.prototype.updateFromAdder = function(gazeType, adderDialog)
    {
        let selectedValues = {};
        this.allFieldIds
            .forEach(function (itemSetId) {
                selectedValues[itemSetId] = this.getSelectedValueFromAdderDialog(adderDialog, itemSetId);
            }.bind(this));

        this.assignAdderValuesToForm(gazeType, selectedValues);
        this.assignAdderValuesToDisplay(gazeType, selectedValues);
        this.switchAdderButtonsForGazeType(gazeType, false);
    };

    NinePositionsReadingController.prototype.removeGazeTypeRecord = function (gazeType) {
        let gazeTypeContainer = this.getGazeTypeContainer(gazeType);

        gazeTypeContainer.querySelectorAll('input[type="hidden"]')
            .forEach(input => input.setAttribute('disabled', 'disabled'));
        gazeTypeContainer.querySelector('.js-display-horizontal').innerHTML = "";
        gazeTypeContainer.querySelector('.js-display-vertical').innerHTML = "";

        this.switchAdderButtonsForGazeType(gazeType, true);
    };

    NinePositionsReadingController.prototype.assignAdderValuesToForm = function (gazeType, selectedValues) {
        let gazeTypeContainer = this.getGazeTypeContainer(gazeType);

        for (const [key, value] of Object.entries(selectedValues)) {
            let valueInput = gazeTypeContainer.querySelector('[data-adder-input-id="' + key + '"]');
            valueInput.value = value;
        }
        gazeTypeContainer.querySelectorAll('input[type="hidden"]')
            .forEach(input => input.removeAttribute('disabled'));
    };

    NinePositionsReadingController.prototype.getOptionDisplayValue = function (options, selectedId)
    {
        return options[options.findIndex(option => option.id === selectedId.toString())].label;
    };

    NinePositionsReadingController.prototype.assignAdderValuesToDisplay = function (gazeType, selectedValues) {
        let gazeTypeContainer = this.getGazeTypeContainer(gazeType);
        this._displayHorizontalValues(gazeTypeContainer, selectedValues);
        this._displayVerticalValues(gazeTypeContainer, selectedValues);
    };

    NinePositionsReadingController.prototype.switchAdderButtonsForGazeType = function (gazeType, showAdderButton) {
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

    NinePositionsReadingController.prototype.getGazeTypeContainer = function (gazeType) {
        return this.options.container.querySelector('.js-gaze-container[data-gaze-type="' + gazeType + '"]');
    };

    /**
     * Itemsets for AdderDialog are universal to prevent unnecessary repetition of elements     *
     * On save updateFromAdder method saves values to relevant Gaze Type form elements
     */
    NinePositionsReadingController.prototype.setupAdders = function () {
        this.adderDialogsByGazeType = [];
        this.options.container.querySelectorAll('[data-adder-trigger="true"]')
            .forEach(function (btn) {
                const gazeType = btn.dataset.gazeType;
                this.adderDialogsByGazeType[gazeType] = new UI.AdderDialog(
                    $.extend(true, this.options.adderDialogOptions, {
                        onOpen: undefined,
                        onSelect: function (event) {
                            this.cleanDeviationSelectionForGazeType(gazeType, event.target.closest('ul').dataset.id);
                        }.bind(this),
                        onReturn: function (adderDialog) {
                            if (!this.validateAdderDialogSelection(adderDialog)) {
                                return false;
                            }
                            this.updateFromAdder(gazeType, adderDialog);
                        }.bind(this),
                        itemSets: this.getAdderItemSets(),
                        openButton: $(btn)
                    })
                );
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

    NinePositionsReadingController.prototype._displayHorizontalValues = function (gazeTypeContainer, selectedValues)
    {
        let horizontalDisplay = selectedValues['horizontal_angle'];
        if (selectedValues.horizontal_prism_position) {
            horizontalDisplay += selectedValues.horizontal_prism_position;
        }
        if (selectedValues.horizontal_e_deviation_id.length) {
            horizontalDisplay += this.getOptionDisplayValue(this.options.horizontalEDeviationOptions, selectedValues.horizontal_e_deviation_id[0]);
        }
        if (selectedValues.horizontal_x_deviation_id.length) {
            horizontalDisplay += this.getOptionDisplayValue(this.options.horizontalXDeviationOptions, selectedValues.horizontal_x_deviation_id[0]);
        }

        gazeTypeContainer.querySelector('.js-display-horizontal').innerHTML = horizontalDisplay;
    };

    NinePositionsReadingController.prototype._displayVerticalValues = function (gazeTypeContainer, selectedValues)
    {
        let verticalDisplay = selectedValues['vertical_angle'];
        if (selectedValues.vertical_prism_position) {
            verticalDisplay += selectedValues.vertical_prism_position;
        }
        if (selectedValues.vertical_deviation_id.length) {
            verticalDisplay += this.getOptionDisplayValue(this.options.verticalDeviationOptions, selectedValues.vertical_deviation_id[0]);
        }

        gazeTypeContainer.querySelector('.js-display-vertical').innerHTML = verticalDisplay;
    };

    function NinePositionsController(options) {
        this.options = $.extend(true, {}, NinePositionsController._defaultOptions, options);

        if (this.options.container === undefined) {
            console.error('must have a container to control');
        }
        if (this.options.patientId === undefined) {
            console.error('patientId required to support reading form requests');
        }

        this.initialiseTriggers();
    }

    NinePositionsController._defaultOptions = {
        container: undefined,
        patientId: undefined,
        templateSelector: '[data-reading-template]',
        readingOptions: {}
    };

    NinePositionsController.prototype.initialiseTriggers = function()
    {
        this.readingButtons = [];
        this.options.container.querySelectorAll('.js-reading')
            .forEach(readingForm => {
                new NinePositionsReadingController($.extend(true, {
                    container: readingForm
                }, this.options.readingOptions));
                this.manageReadingRemovalInContainer(readingForm);
                this.manageReadingAdditionInContainer(readingForm);
            });

        this.manageMultipleReadingButtonVisibilty();
    };

    NinePositionsController.prototype.addReading = function(event)
    {
        event.preventDefault();
        const addReadingLink = event.target;
        addReadingLink.classList.add('disabled');
        addReadingLink.setAttribute('disabled', 'disabled');
        const loaderIcon = addReadingLink.parentElement.querySelector('.js-loader');
        loaderIcon.style.display = '';

        const nextIndex = Util.getNextDataKey('#' + this.options.container.getAttribute('id') + ' .js-reading', 'key');

        let xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    const currentReadings = this.options.container.querySelectorAll('.js-reading');
                    this.addHtmlAndScriptsFromText(
                        xhr.responseText,
                        currentReadings[currentReadings.length -1],
                        function() {
                            const readingContainer = this.options.container.querySelectorAll('.js-reading')[currentReadings.length];
                            new NinePositionsReadingController($.extend(true, {
                                container: readingContainer
                            }, this.options.readingOptions));
                            this.manageReadingRemovalInContainer(readingContainer);
                            this.manageReadingAdditionInContainer(readingContainer);
                            this.manageMultipleReadingButtonVisibilty();
                        }.bind(this));
                } else {
                    console.error('could not load reading form!!');
                    console.log(xhr.responseText);
                }
                loaderIcon.style.display = 'none';
                addReadingLink.classList.remove('disabled');
                addReadingLink.removeAttribute('disabled');
            }
        }.bind(this);

        xhr.open('GET', '/OphCiExamination/NinePositions/ReadingForm?patient_id=' + this.options.patientId + '&index=' + nextIndex);
        xhr.send();
    };

    /**
     * inline scripts will not be actioned when added as a block of text to the dom.
     * This extracts the script content and appends it to the main container, whilst
     * appending the rest of the html to the given dom element.
     *
     * @param text
     * @param appendTo
     */
    NinePositionsController.prototype.addHtmlAndScriptsFromText = function(text, appendTo, callback)
    {
        let tempDiv = document.createElement('div');
        tempDiv.innerHTML = text;
        let scriptContent = document.createTextNode(
            Array.from(tempDiv.getElementsByTagName('script'))
                .map(node => node.textContent )
                .join("\n")
        );
        let scriptNode = document.createElement('script');
        scriptNode.appendChild(scriptContent);

        Array.from(tempDiv.childNodes)
            .forEach(node => {
                if (node.tagName !== 'SCRIPT') {
                    appendTo.after(node);
                    appendTo = node;
                }
            });
        this.options.container.appendChild(scriptNode);
        if (callback !== undefined) {
            callback();
        }
    };

    NinePositionsController.prototype.manageReadingAdditionInContainer = function(container)
    {
    const addReadingLink = container.querySelector('.js-add-reading');
        if (addReadingLink) {
            addReadingLink.addEventListener('click', this.addReading.bind(this));
        }
    };

    NinePositionsController.prototype.manageReadingRemovalInContainer = function(container)
    {
        const buttonContainer = container.querySelector('.js-reading-buttons');
        if (!buttonContainer) {
            return;
        }

        this.readingButtons.push(buttonContainer);
        buttonContainer.querySelector('.js-remove-reading')
            .addEventListener('click', this.removeReading.bind(this, container));
    };

    NinePositionsController.prototype.removeReading = function(readingContainer, event)
    {
        event.preventDefault();

        if (this.readingButtons.length <= 1) {
            return;
        }

        // filter to stop tracking this remover
        this.readingButtons = this.readingButtons
            .filter(buttons => buttons.closest('.js-reading').dataset.key !== readingContainer.dataset.key);

        readingContainer.remove();
        this.manageMultipleReadingButtonVisibilty();
    };

    NinePositionsController.prototype.manageMultipleReadingButtonVisibilty = function()
    {
        this.showRemoveButtonsIfMultipleReadingsInForm();
        this.onlyShowAddButtonInLastReadingForm();
    };

    NinePositionsController.prototype.showRemoveButtonsIfMultipleReadingsInForm = function()
    {
        const showRemovalButton = this.readingButtons.length > 1;
        this.readingButtons.forEach(buttonsContainer => {
            if (showRemovalButton) {
                buttonsContainer.querySelector('.js-remove-reading').style.display = '';
            } else {
                buttonsContainer.querySelector('.js-remove-reading').style.display = 'none';
            }
        });
    };

    NinePositionsController.prototype.onlyShowAddButtonInLastReadingForm = function()
    {
        const readingContainers = this.options.container.querySelectorAll('.js-reading');
        // shouldn't happen
        if (readingContainers.length === 0) {
            console.error('should always be able to find at least one reading container.');
            return;
        }

        Array.prototype.slice.apply(readingContainers, [0, -1])
            .forEach(container => container.querySelector('.js-add-reading').style.display = 'none');

        const lastAddButton = readingContainers
            .item(readingContainers.length - 1)
            .querySelector('.js-add-reading');
        lastAddButton.style.display = '';
        // lastAddButton.closest('.js-reading-buttons').style.display = '';
    };

    exports.NinePositionsController = NinePositionsController;


})(OpenEyes.OphCiExamination, OpenEyes.Util, OpenEyes.UI);

OpenEyes.OphCiExamination.ninePositionsEyedrawListener = function(drawing)
{
    let canvas = drawing.canvas;
    let container = canvas.closest('.js-reading');
    let controller = container.edController;
    if (!controller) {
        controller =  new OpenEyes.OphCiExamination.NinePositionsEyedrawController({container: container});
    }

    controller.initialiseDrawing(drawing);
};
