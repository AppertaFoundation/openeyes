this.OpenEyes = this.OpenEyes || {};
this.OpenEyes.UI = this.OpenEyes.UI || {};

(function (exports) {
    /**
     * @param {Object} options
     * @constructor
     */
    function EventAutoGenerateEsign(options) {
        if (typeof options !== "undefined") {
            this.options = $.extend(true, {}, EventAutoGenerateEsign._defaultOptions, options);
        }

        this.init();
        this.hideOrShowFields();
        this.bindEvents();
    }

    EventAutoGenerateEsign._defaultOptions = {
        "pin_required_for_event_type": [],
        "container_selector": "",
        "pin_container_selector": "",
        "draft_container_selector": "",
        "event_types": ['correspondence', 'prescription'],
    };

    EventAutoGenerateEsign.prototype.init = function () {
        this.container = document.querySelector(this.options.container_selector);
        this.pinContainer = document.querySelector(this.options.pin_container_selector);
        this.draftContainer = document.querySelector(this.options.draft_container_selector);
        this.checkboxes = this.container.querySelectorAll('.js-auto-generate-event-checkbox');
    };

    EventAutoGenerateEsign.prototype.bindEvents = function () {
        const controller = this;

        controller.container.querySelectorAll('.js-auto-generate-event-checkbox, .js-save-as-draft-inputs').forEach(function (clickedCheckbox) {
            clickedCheckbox.addEventListener('change', controller.hideOrShowFields.bind(controller));
        });
    };

    EventAutoGenerateEsign.prototype.hideOrShowFields = function () {
        this.togglePinField();
        this.toggleDraftField();
        this.toggleDraftCheckboxes();
    };

    EventAutoGenerateEsign.prototype.togglePinField = function () {
        const controller = this;
        let showField = false;

        controller.checkboxes.forEach(function (checkbox) {
            const eventType = checkbox.dataset.eventType;

            if (!controller.saveAsDraftCheckboxCheckedForEventType(eventType) &&
                checkbox.checked && controller.options.pin_required_for_event_type[eventType]) {
                showField = true;
            }
        });

        controller.disableOrEnableContainer(controller.pinContainer, showField);
    };

    EventAutoGenerateEsign.prototype.toggleDraftField = function () {
        const controller = this;
        let showField = false;

        controller.checkboxes.forEach(function (checkbox) {
            if (checkbox.checked) {
                showField = true;
            }
        });

        controller.disableOrEnableContainer(controller.draftContainer, showField);
    };

    EventAutoGenerateEsign.prototype.toggleDraftCheckboxes = function () {
        const controller = this;

        controller.options.event_types.forEach(function (eventType) {
            let checkedEventTypeCheckboxes = [...controller.checkboxes].filter(
                (checkbox) => checkbox.dataset.eventType === eventType && checkbox.checked);

            let showField = checkedEventTypeCheckboxes.length > 0;

            controller.showOrHideDraftCheckbox(eventType, showField);

        });
    };

    EventAutoGenerateEsign.prototype.showOrHideDraftCheckbox = function (eventType, showField) {
        const draftCheckboxContainer = this.draftContainer.querySelector(
            `.js-save-as-draft-inputs[data-event-type="${eventType}"]`)
            .closest('.js-save-as-draft-containers');

        const noPrescribeRightsTooltip = this.draftContainer.querySelector(
            '.js-no-prescribe-rights-tooltip');

        this.disableOrEnableContainer(draftCheckboxContainer, showField);

        if(noPrescribeRightsTooltip !== null) {
            this.disableOrEnableContainer(noPrescribeRightsTooltip, showField);
        }
    };


    EventAutoGenerateEsign.prototype.saveAsDraftCheckboxCheckedForEventType = function (eventType) {
        const saveDraftForEventTypeCheckbox = this.container.querySelector(
            `.js-save-as-draft-inputs[data-event-type="${eventType}"]`);

        let isChecked = false;

        if (saveDraftForEventTypeCheckbox !== null) {
            isChecked = saveDraftForEventTypeCheckbox.checked;
        }

        return isChecked;
    };

    EventAutoGenerateEsign.prototype.disableOrEnableContainer = function (container, showField) {
        if (showField) {
            container.querySelectorAll('input').forEach(function (containerInput) {
                if (typeof containerInput.dataset.cannotBeEnabled === "undefined" || !containerInput.dataset.cannotBeEnabled) {
                    containerInput.disabled = false;
                }
            });
            container.style.display = '';
        } else {
            container.style.display = 'none';
            container.querySelectorAll('input').forEach(function (containerInput) {
                if (typeof containerInput.dataset.cannotBeEnabled === "undefined" || !containerInput.dataset.cannotBeEnabled) {
                    containerInput.disabled = true;
                }
            });
        }
    };

    exports.EventAutoGenerateEsign = EventAutoGenerateEsign;
})(OpenEyes.UI);