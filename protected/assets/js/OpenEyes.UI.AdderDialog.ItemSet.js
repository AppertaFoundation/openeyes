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
        'signs':{'minus' : '-' , 'plus' : '+'},
        'supportDecimalValues' : false,
        'decimalValues' : ['.00' , '.25' , '.50' , '.75'],
        'splitIntegerNumberColumns': [], // array with min&max interval allowed for each column
    };

  ItemSet.prototype.create = function () {

  };

  exports.ItemSet = ItemSet;

}(OpenEyes.UI.AdderDialog));