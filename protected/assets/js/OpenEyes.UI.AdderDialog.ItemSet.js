(function (exports) {

  'use strict';

  function ItemSet(items, header, options) {
    this.header = header;
    this.items = items;
    this.options = $.extend(true, {}, ItemSet._defaultOptions, options);
    this.create();
  }

  ItemSet._default_options = {
    'multiSelect': false,
  };

  ItemSet.prototype.create = function () {

  };

  exports.ItemSet = ItemSet;

}(OpenEyes.UI.AdderDialog));