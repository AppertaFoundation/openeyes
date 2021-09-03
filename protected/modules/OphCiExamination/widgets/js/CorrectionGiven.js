var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};
OpenEyes.Util = OpenEyes.Util || {};
OpenEyes.UI = OpenEyes.UI || {};

(function (exports, Util, UI) {
    const BaseController = UI.ElementController;

    /**
     * Correction Given syncs with Elements that record refraction values, offering
     * them as quick pick options (as found).
     *
     * @param options
     * @constructor
     */
    function CorrectionGiven(options) {
        this.options = $.extend(true, {}, CorrectionGiven._defaultOptions, options);

        // don't use parent setup, because form and adder components
        // have to be handled differently to respond to changes to available
        // refraction values
        this.listenForElementRemoval();

        this.asFoundRefractions = {};
        this.validElementTypeIds = undefined;

        this.asFoundField = this.options.container.querySelector('.js-as-found');
        this.asFoundElementTypeIdField = this.options.container.querySelector('.js-as-found-element-type-id');
        this.refractionField = this.options.container.querySelector('.js-refraction');
        this.refractionDisplay = this.options.container.querySelector('.js-refraction-display');
        this.labelAsFound = this.options.container.querySelector('.js-label-as-found');
        this.labelAsAdjusted = this.options.container.querySelector('.js-label-as-adjusted');

        this.initialiseTriggers();
        this.requestAvailableRefractions();
    }

    Util.inherits(BaseController, CorrectionGiven);

    CorrectionGiven._defaultOptions = {
        side: undefined,
        asFoundElementTypes: [],
        letAdderDialogHandleOpenButton: false,
        adderDialogOptions: {
            deselectOnReturn: true,
            listFilter: true,
            filterListId: 'order-as',
            listForFilterId: 'refraction',
        },
    };

    CorrectionGiven.prototype.initialiseTriggers = function()
    {
        this.listenForAdderDialogTrigger();
        this.listenForRefractionUpdates();
    };

    CorrectionGiven.prototype.listenForAdderDialogTrigger = function()
    {
        this.getAdderOpenButton().addEventListener('click', this.openAdder.bind(this));
    };

    CorrectionGiven.prototype.openAdder = function(event)
    {
        event.stopPropagation();
        event.preventDefault();
        this.setupAdder(this.defineAdderItemSets());
        this.adder.open();
    };

    /**
     * Process the user selection
     *
     * @param adderDialog
     * @param selectedItems
     */
    CorrectionGiven.prototype.updateFromAdder = function(adderDialog, selectedItems)
    {
        // selections always returned as arrays, so normalise by picking the first entry
        const orderAs = this.getFormattedItemSetValueFromAdderDialog(
            adderDialog,
            adderDialog.options.itemSets[0],
            {})[0];
        const refraction = this.getFormattedItemSetValueFromAdderDialog(
            adderDialog,
            adderDialog.options.itemSets[1],
            {})[0];

        if (orderAs === '__found__') {
            this.setFormRefractionAsFound(refraction);
        } else {
            this.setFormRefractionAdjusted();
        }

        return true;
    };

    /**
     * Hide the refraction input and display the fixed value from the given selection
     *
     * @param refractionSelectionId
     */
    CorrectionGiven.prototype.setFormRefractionAsFound = function(refractionSelectionId)
    {
        this.asFoundField.value = "1";
        const [elementTypeId, refraction] = refractionSelectionId.split(':');
        this.asFoundElementTypeIdField.value = elementTypeId;
        this.refractionField.value = refraction;
        this.refractionDisplay.textContent = refraction + ' (' + this.getElementTypeLabel(elementTypeId) + ')';
        this.toggleDomElement(this.refractionField, false);
        this.toggleDomElement(this.refractionDisplay, true);
        this.toggleDomElement(this.labelAsAdjusted, false);
        this.toggleDomElement(this.labelAsFound, true);
    };

    /**
     * Set form values appropriately and display the input field for manual
     * refraction entry
     */
    CorrectionGiven.prototype.setFormRefractionAdjusted = function()
    {
        this.asFoundField.value = "0";
        this.asFoundElementTypeIdField.value = "";
        this.toggleDomElement(this.refractionField, true);
        this.toggleDomElement(this.refractionDisplay, false);
        this.toggleDomElement(this.labelAsAdjusted, true);
        this.toggleDomElement(this.labelAsFound, false);
    };

    CorrectionGiven.prototype.removeAdder = function()
    {
        if (this.adder) {
            this.adder.remove();
            delete this.adder;
        }
    };

    CorrectionGiven.prototype.defineAdderItemSets = function(formContainer)
    {
        return [
            new UI.AdderDialog.ItemSet(
                [
                    {
                        label: 'Found',
                        id: '__found__',
                        filter_value: '__found__'
                    },
                    {
                        label: 'Adjusted',
                        id: '__adjusted__',
                        filter_value: '__adjusted__'
                    },
                ],
                {
                    header: 'Order as',
                    id: 'order-as',
                    required: true
                }
            ),
            new UI.AdderDialog.ItemSet(
                this.getRefractionOptionsForItemSet(),
                {
                    header: 'Refraction',
                    id: 'refraction',
                    requiresItemSet: 'order-as',
                    required: true
                }
            )
        ];
    };

    CorrectionGiven.prototype.getRefractionOptionsForItemSet = function()
    {
        let result = [];

        Object.keys(this.asFoundRefractions)
            .forEach(elementTypeId => result.push.apply(
                result,
                this.getRefractionOptionsForElementTypeId(elementTypeId)
            ));

        result.push({
            id: '__adjusted__',
            label: 'Input Refraction',
            filter_value: '__adjusted__'
        });

        return result;
    };

    CorrectionGiven.prototype.getRefractionOptionsForElementTypeId = function(elementTypeId)
    {
        const elementName = this.getElementTypeLabel(elementTypeId);

        return this.asFoundRefractions[elementTypeId].map(refraction => {
            return {
                id: elementTypeId + ':' + refraction,
                label: refraction + ' (' + elementName + ')',
                elementTypeId: elementTypeId,
                refraction: refraction,
                filter_value: '__found__'
            };
        });
    };

    CorrectionGiven.prototype.handleElementRemoval = function()
    {
        CorrectionGiven._super.prototype.handleElementRemoval.call(this);
        this.stopListeningForRefractionUpdates();
    };

    CorrectionGiven.prototype.isValidElementTypeId = function(elementTypeId)
    {
        if (!this.validElementTypeIds) {
            this.validElementTypeIds = this.options.asFoundElementTypes.map(elementType => elementType.id);
        }

        return this.validElementTypeIds.includes(elementTypeId);
    };

    CorrectionGiven.prototype.getElementTypeLabel = function(elementTypeId)
    {
        return this.options.asFoundElementTypes
            .filter(elementType => elementType.id === elementTypeId)[0].label.toLowerCase();
    }

    CorrectionGiven.prototype.setAsFoundRefractionsForElementTypeId = function(elementTypeId, refractions)
    {
        if (this.asFoundRefractions[elementTypeId] === undefined || this.asFoundRefractions[elementTypeId] !== refractions) {
            this.removeAdder();
            this.asFoundRefractions[elementTypeId] = refractions;
        }
    };

    CorrectionGiven.prototype.removeAsFoundRefractionsForElementTypeId = function(elementTypeId)
    {
        if (this.asFoundRefractions[elementTypeId] !== undefined) {
            if (this.asFoundRefractions[elementTypeId].length) {
                this.removeAdder();
            }
            delete this.asFoundRefractions[elementTypeId];
        }
    };

    CorrectionGiven.prototype.listenForRefractionUpdates = function()
    {
        this.refractionUpdateListener = this.updateAsFoundRefractionsFromEvent.bind(this);
        document.addEventListener('OpenEyes.OphCiExamination.RefractionUpdate', this.refractionUpdateListener);
        this.refractionRemovalListener = this.removeAsFoundRefractionsFromEvent.bind(this);
        document.addEventListener('OpenEyes.OphCiExamination.RefractionSourceRemoved', this.refractionRemovalListener);
    };

    CorrectionGiven.prototype.stopListeningForRefractionUpdates = function()
    {
        document.removeEventListener('OpenEyes.OphCiExamination.RefractionUpdate', this.refractionUpdateListener);
    }

    CorrectionGiven.prototype.requestAvailableRefractions = function()
    {
        document.dispatchEvent(
            new Event('OpenEyes.OphCiExamination.ReadyForRefractionUpdates')
        );
    };

    CorrectionGiven.prototype.updateAsFoundRefractionsFromEvent = function(event)
    {
        const source = event.detail.source;
        const elementTypeId = source.getElementTypeId();
        if (!this.isValidElementTypeId(elementTypeId)) {
            return;
        }

        const refractionsForSide = source.getRefractionsForSide(this.options.side);
        if (refractionsForSide !== undefined) {
            this.setAsFoundRefractionsForElementTypeId(elementTypeId, refractionsForSide);
        }
    };

    CorrectionGiven.prototype.removeAsFoundRefractionsFromEvent = function(event)
    {
        const source = event.detail.source;
        this.removeAsFoundRefractionsForElementTypeId(source.getElementTypeId());
    };

    exports.CorrectionGivenController = CorrectionGiven;

}(OpenEyes.OphCiExamination, OpenEyes.Util, OpenEyes.UI));