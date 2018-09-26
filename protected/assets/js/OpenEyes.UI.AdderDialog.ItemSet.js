(function (exports) {

  'use strict';

  function ItemSet(items, options) {
    this.items = items;
    this.options = $.extend(true, {}, ItemSet._defaultOptions, options);
    this.create();
  }

  ItemSet._default_options = {
    'multiSelect': false,
    'mandatory': false,
    'header': null,
    'id': null,
  };

  ItemSet.prototype.create = function () {

  };

	/**
	 * 	If itemset has default value to display, return its index in the list.
	 */
  ItemSet.prototype.getScrollIndex = function() {
  	var result = 0;
		$(this.items).each(function (index, item) {
			if (item['set-default']){
				result = index;
			}
		});
		return result;
	};

  exports.ItemSet = ItemSet;

}(OpenEyes.UI.AdderDialog));