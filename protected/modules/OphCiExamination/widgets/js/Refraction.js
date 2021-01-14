var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};
OpenEyes.Util = OpenEyes.Util || {};
OpenEyes.UI = OpenEyes.UI || {};

(function (exports, Util, UI) {

    const BaseController = UI.ElementController.MultiRow;

    function Refraction(options) {
        options = $.extend(true, {}, Refraction._defaultOptions, options);

        BaseController.call(this, options);
        this.initialiseTriggers();
    }

    Util.inherits(BaseController, Refraction);

    Refraction._defaultOptions = {
        typeOtherId: '__other__',
        adderDialogOptions: {
           deselectOnReturn: true
        }
    };

    Refraction.prototype.initialiseTriggers = function()
    {
        this.listenForRefractionControllers();

    };

    Refraction.prototype.onlyDisplayRelevantFieldsForRow = function(formContainer)
    {
        Refraction._super.prototype.onlyDisplayRelevantFieldsForRow.call(this, formContainer);
        const typeSelectorField = formContainer.querySelector('[data-adder-id$="_type_id"]');
        if (typeSelectorField.value === this.options.typeOtherId) {
            typeSelectorField.value = '';
            this.toggleFormField(typeSelectorField, false);
            this.toggleFormField(formContainer.querySelector('[name$="type_other\\]"]'), true);
        }
    };

    Refraction.prototype.updateFromAdder = function(adderDialog, selectedItems)
    {
        Refraction._super.prototype.updateFromAdder.call(this, adderDialog, selectedItems);
        this.dispatchRefractionUpdateEvent();
    };

    Refraction.prototype.handleElementRemoval = function()
    {
        Refraction._super.prototype.handleElementRemoval.call(this);
        this.stopListeningForRefractionControllers();
        document.dispatchEvent(
            new CustomEvent('OpenEyes.OphCiExamination.RefractionSourceRemoved', {detail: {source: this}})
        );
    };

    /**
     * METHODS FOR REFRACTION INTEGRATION
     */
    Refraction.prototype.listenForRefractionControllers = function()
    {
        // track for removal
        this.refractionListener = this.dispatchRefractionUpdateEvent.bind(this);
        document.addEventListener('OpenEyes.OphCiExamination.ReadyForRefractionUpdates', this.refractionListener);
    };

    Refraction.prototype.stopListeningForRefractionControllers = function()
    {
        document.removeEventListener('OpenEyes.OphCiExamination.ReadyForRefractionUpdates', this.refractionListener);
    };

    Refraction.prototype.dispatchRefractionUpdateEvent = function()
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

    Refraction.prototype.getElementTypeId = function()
    {
        if (!this.elementTypeId) {
            this.elementTypeId = this.options.elementTypeId
                ? this.options.elementTypeId
                : this.options.container.closest('section').dataset.elementTypeId;
        }
        return this.elementTypeId;
    };

    Refraction.prototype.getRefractionsForSide = function(side)
    {
        if (this.options.side === side) {
            return Array.from(this.options.container.querySelector(this.options.rowsContainerSelector).children)
                .map(row => this.getRefractionFromRow(row));
        }
        return undefined; // undefined indicates not for the requested side
    };

    Refraction.prototype.getRefractionFromRow = function(row)
    {
        return row.querySelector('.js-sphere').value
            + ' / '
            + row.querySelector('.js-cylinder').value
            + ' x '
            + row.querySelector('.js-axis').value;
    };

    exports.RefractionController = Refraction;
}(OpenEyes.OphCiExamination, OpenEyes.Util, OpenEyes.UI));