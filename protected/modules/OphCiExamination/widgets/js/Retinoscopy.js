var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};
OpenEyes.Util = OpenEyes.Util || {};
OpenEyes.UI = OpenEyes.UI || {};

(function (exports, Util, UI) {
    const BaseController = UI.ElementController;

    function Retinoscopy(options) {
        options = $.extend(true, {}, Retinoscopy._defaultOptions, options);

        BaseController.call(this, options);

        this.angleField = this.options.container.querySelector('[data-adder-input-id="angle"]');
        this.power1Field = this.options.container.querySelector('[data-adder-input-id="power1"]');
        this.power2Field = this.options.container.querySelector('[data-adder-input-id="power2"]');
        this.refractionField = this.options.container.querySelector(this.options.refractionFieldSelector);
        this.refractionDisplay = this.options.container.querySelector(this.options.refractionDisplaySelector);

        this.initialiseTriggers();
    }

    Util.inherits(BaseController, Retinoscopy);

    Retinoscopy._defaultOptions = {
        refractionFieldSelector: '.js-refraction-field',
        refractionDisplaySelector: '.js-refraction-display',
        elementTypeId: undefined
    };

    Retinoscopy.prototype.initialiseTriggers = function()
    {
        this.listenForRefractionControllers();
    }


    Retinoscopy.prototype.initialiseDrawing = function(drawing)
    {
        drawing.registerForNotifications(this, 'drawingNotificationsHandler');
        const powerCross = drawing.firstDoodleOfClass('RetinoscopyPowerCross');
        if (powerCross) {
            this._updateRefractionFromDoodle(powerCross);
        }
    };

    Retinoscopy.prototype.onAdderOpen = function(adderDialog) {
        // ensure that options are selected
        // adderDialog.setSelectedItemsForItemSet(adderDialog.options.itemSets[0], this.weightUnitsField.value);
        adderDialog.setSelectedItemsForItemSet(adderDialog.options.itemSets[1], this.angleField.value);
        adderDialog.setSelectedItemsForItemSet(adderDialog.options.itemSets[2], this.power1Field.value);
        adderDialog.setSelectedItemsForItemSet(adderDialog.options.itemSets[3], this.power2Field.value);
    };

    Retinoscopy.prototype.updateFromAdder = function(adderDialog, selectedItems)
    {
        Retinoscopy._super.prototype.updateFromAdder.call(this, adderDialog, selectedItems);
        this.setWorkingDistanceValueForEyedraw();
    };

    /**
     * Working distance is a lookup with a display value that doesn't reflect the values used
     * by Eyedraw in the diagram. To resolve this, we extract the value from the drop down
     * and set a different hidden field with this numeric value. That hidden field is bound to
     * Eyedraw for the refraction calculations it performs.
     */
    Retinoscopy.prototype.setWorkingDistanceValueForEyedraw = function()
    {
        const workingDistanceValue = this.options.container
            .querySelector('[data-adder-wd-select-field="true"]')
            .selectedOptions[0].dataset.value;
        const workingDistanceValueField = this.options.container
            .querySelector('[data-adder-wd-value-field="true"]');

        workingDistanceValueField.value = workingDistanceValue;
        workingDistanceValueField.dispatchEvent(new Event('change'));
    };

    Retinoscopy.prototype.drawingNotificationsHandler = function(msgArray)
    {
        if (this._powerCrossDoodleAdded(msgArray)) {
            return this._updateRefractionFromDoodle(msgArray.object);
        }
        if (this._powerCrossDoodleUpdated(msgArray)) {
            return this._updateRefractionFromDoodle(msgArray.object.doodle);
        }
    };

    Retinoscopy.prototype._powerCrossDoodleAdded = function(msgArray)
    {
        // object is the added doodle for this event
        return msgArray.eventName === 'doodleAdded' && msgArray.object.className === 'RetinoscopyPowerCross';
    };

    Retinoscopy.prototype._powerCrossDoodleUpdated = function(msgArray)
    {
        // object is a structure containing the doodle for this event
        return msgArray.eventName === 'parameterChanged' && msgArray.object && msgArray.object.doodle.className === 'RetinoscopyPowerCross';
    };

    Retinoscopy.prototype._updateRefractionFromDoodle = function(doodle)
    {
        this.refractionField.value =
            this.refractionDisplay.textContent = doodle.description();

        this.dispatchRefractionUpdateEvent();
    };

    Retinoscopy.prototype.handleElementRemoval = function()
    {
        Retinoscopy._super.prototype.handleElementRemoval.call(this);
        this.stopListeningForRefractionControllers();
        document.dispatchEvent(
            new CustomEvent('OpenEyes.OphCiExamination.RefractionSourceRemoved', {detail: {source: this}})
        );
    };

    /**
     * METHODS FOR REFRACTION INTEGRATION
     */
    Retinoscopy.prototype.listenForRefractionControllers = function()
    {
        // track for removal
        this.refractionListener = this.dispatchRefractionUpdateEvent.bind(this);
        document.addEventListener('OpenEyes.OphCiExamination.ReadyForRefractionUpdates', this.refractionListener);
    };

    Retinoscopy.prototype.stopListeningForRefractionControllers = function()
    {
        document.removeEventListener('OpenEyes.OphCiExamination.ReadyForRefractionUpdates', this.refractionListener);
    };

    Retinoscopy.prototype.dispatchRefractionUpdateEvent = function()
    {
        if (this.refractionUpdateEvent) {
            clearTimeout(this.refractionUpdateEvent);
        }

        this.refractionUpdateEvent = setTimeout(function() {
            document.dispatchEvent(
                new CustomEvent('OpenEyes.OphCiExamination.RefractionUpdate', {detail: {source: this}})
            );
        }.bind(this), 200);
    };

    Retinoscopy.prototype.getElementTypeId = function()
    {
        return this.options.elementTypeId;
    };

    Retinoscopy.prototype.getRefractionsForSide = function(side)
    {
        if (this.options.side === side) {
            return [this.refractionDisplay.textContent];
        }
        return undefined;
    };

    exports.RetinoscopyController = Retinoscopy;
})(OpenEyes.OphCiExamination, OpenEyes.Util, OpenEyes.UI);

OpenEyes.OphCiExamination.retinoscopyEyedrawListener = function(drawing)
{
    let canvas = drawing.canvas;
    let container = canvas.closest('.js-retinoscopy-form');
    let controller = container.controller;
    if (!controller) {
        controller = container.controller = new OpenEyes.OphCiExamination.RetinoscopyController({
            container: container,
            side: container.dataset.side,
            elementTypeId: container.closest('section').dataset.elementTypeId
        });
    }

    controller.initialiseDrawing(drawing);
};