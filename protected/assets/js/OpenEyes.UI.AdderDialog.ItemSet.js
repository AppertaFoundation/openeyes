(function (exports) {

    'use strict';

    function ItemSet(items, options) {
        this.items = items;
        this.options = $.extend(true, {}, ItemSet._default_options, options);
        this.create();
    }

    ItemSet._default_options = {
        'multiSelect': false,
        'mandatory': false,
        'header': null,
        'id': null,
        'supportSigns': false,
        'style': "",
        'liClass': "",
        'signs':{'minus' : '-' , 'plus' : '+'},
        'supportDecimalValues' : false,
        'decimalValues' : ['.00' , '.25' , '.50' , '.75'],
        'decimalValuesType': null,
        'splitIntegerNumberColumns': [], // array with min&max interval allowed for each column
        'splitIntegerNumberColumnsTypes': [],
        'deselectOnReturn' : true,
        'resetSelectionToDefaultOnReturn' : false,
        'conditionalFlowMaps' : [],
        'hideByDefault': false,
    };

    ItemSet.prototype.create = function () {
    };

    exports.ItemSet = ItemSet;

}(OpenEyes.UI.AdderDialog));
