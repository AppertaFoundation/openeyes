(function(exports) {

    const Util = {};

    Util.parseFieldOptions = function(element, options) {
        return $.extend(true, {}, {
            header: element.dataset.adderHeader,
            id: element.dataset.adderId ? element.dataset.adderId : element.getAttribute('id'),
            mandatory: element.getAttribute('required'),
            requiresItemSet: element.dataset.adderRequiresItemSet,
            requiresItemSetValues: element.dataset.adderRequiresItemSetValues ? JSON.parse(element.dataset.adderRequiresItemSetValues) : null,
            showInfo: element.dataset.adderShowInfo,
            multiSelect: element.getAttribute('multiple'),
            supportSigns: element.dataset.adderItemSetSupportSign ? element.dataset.adderItemSetSupportSign : false,
            supportDecimalValues: element.dataset.adderItemSetSupportDecimalValues ? element.dataset.adderItemSetSupportDecimalValues : false,
            // force this to be set as otherwise defaults to null, making it hard to parse in AdderDialog
            decimalValuesType: element.dataset.adderItemSetDecimalValuesType ? element.dataset.adderItemSetDecimalValuesType :
                (element.dataset.adderItemSetSupportDecimalValues ? 'decimalValue' : null)
        }, options);
    };

    /**
     * parse options that are only applicable on text fields
     * @param element
     * @param options
     */
    Util.parseTextFieldOptions = function(element, options) {
        if (element.dataset.adderItemSetType === 'float') {
            options.generateFloatNumberColumns = {
                decimalPlaces: element.dataset.adderItemSetDecimalPlaces || 0,
                minValue: element.dataset.adderItemSetMin || 0,
                maxValue: element.dataset.adderItemSetMax || 999
            };
        }
        return options;
    };

    /**
     * Build an ItemSet from a form select element.
     * Supports the following properties for automated option setting:
     *
     * data-adder-header
     * data-adder-id
     *
     * @param element
     * @param options
     * @return {exports.ItemSet}
     */
    Util.itemSetFromDropdown = function(element, options) {
        let itemSetOptions = Util.parseFieldOptions(element, options);

        let itemOptions = [].filter.call(
            element.querySelectorAll('option'),
            function (optionElement) {
                return optionElement.value !== undefined && optionElement.value !== '';
            })
            .map(function (optionElement) {
                return {
                    label: optionElement.textContent,
                    id: optionElement.value,
                    selected: optionElement.selected,
                    filter_value: optionElement.dataset.filterValue
                };
            });

        return new exports.ItemSet(itemOptions, itemSetOptions);
    };

    Util.itemSetFromTextfield = function(element, options) {
        let itemSetOptions = Util.parseTextFieldOptions(element, Util.parseFieldOptions(element, options));
        return new exports.ItemSet([], itemSetOptions);
    };

    Util.itemSetFromFormField = function(element, options) {
        if (element.nodeName === 'SELECT') {
            return Util.itemSetFromDropdown(element, options);
        }
        if (element.nodeName === 'INPUT') {
            return Util.itemSetFromTextfield(element, options);
        }
        console.warn('unsupported node for automatic itemset generation: ' + element.nodeName);
    };

    exports.Util = Util;
}(OpenEyes.UI.AdderDialog));