(function (exports, Util, EventEmitter) {

    'use strict';

    /**'
     *
     * @param {object} options
     * @constructor
     */
    function AdderDialog(options) {

        this.searchRequest = null;
        this.isOpen = false;
        this.blackoutDiv = null;

        EventEmitter.call(this);
        this.options = $.extend(true, {}, AdderDialog._defaultOptions, options);
        this.create();
    }

    Util.inherits(EventEmitter, AdderDialog);

    /**
     * The default AdderDialog options. Custom options will be merged with these.
     * @name OpenEyes.UI.AdderDialog#_defaultOptions
     * @property {OpenEyes.UI.AdderDialog.ItemSet[]} [itemSets=null] - The lists of items that the user can select from
     * @property {object} [openButton=null] - The DOM handle for the button used to open the popup
     * @property {Function} [onOpen=null] - A callback to be called when the popup is opened
     * @property {Function} [onClose=null] - A callback to be called when the popup is closed
     * @property {Function} [onSelect=null] - A callback to be called when an item is selected
     * @property {Function} [onReturn=null] - A callback to be called when the add button is clicked
     * @property {boolean} [deselectOnReturn=true] - Whether all items should be deselected when the popup is added
     * @property {string} [id=null] - The ID of the popup div
     * @property {string} [popupClass='oe-add-select-search auto-width'] - The classes to use for the popup
     * @property {string} [liClass='auto-width'] - The class to use for the items
     * @property {boolean} [createBlackoutDiv] - Whether a blackout div should be created, closing the popup if the user clicks anywhere else
     * @private
     */
    AdderDialog._defaultOptions = {
        itemSets: [],
        openButton: null,
        onOpen: null,
        onClose: null,
        onSelect: null,
        onReturn: null,
        returnOnSelect: false,
        deselectOnReturn: true,
        id: null,
        popupClass: 'oe-add-select-search auto-width',
        liClass: 'auto-width',
        searchOptions: null,
        width: null,
        createBlackoutDiv: true,
        enableCustomSearchEntries: false,
        searchAsTypedPrefix: 'As typed: ',
        searchAsTypedItemProperties: {},
        filter: false,
        filterDataId: "",
        listFilter:false,
        filterListId: "",
        listForFilterId: "",
        booleanSearchFilterEnabled: false,
        booleanSearchFilterLabel: '',
        booleanSearchFilterURLparam: ''
    };

    /**
     * Creates and stores the adder dialog container
     * @name OpenEyes.UI.AdderDialog#create
     */
    AdderDialog.prototype.create = function () {
        let dialog = this;

        let content = $('<div />', {class: this.options.popupClass, id: this.options.id});
        if (this.options.width) {
            content.css('width', this.options.width);
        }
        let $closeButton = $('<div />', {class: 'close-icon-btn'})
            .append($('<i />', {class: 'oe-i remove-circle medium pro-theme selected'}));
        content.append($closeButton);

        let $addButton = $('<div />', {
            class: 'add-icon-btn'
        }).append($('<i />', {class: 'oe-i plus pad pro-theme selected'}));
        $addButton.append('Click to add');


        content.append($addButton);

        this.setOpenButton(this.options.openButton);
        this.setCloseButton($(content).find('.close-icon-btn'));
        this.setAddButton($addButton);

        content.insertAfter(this.options.openButton);
        this.popup = content;
        this.generateContent();

        if (this.options.searchOptions) {
            this.generateSearch();
        }

        this.popup.hide();

        if (this.options.onSelect) {
            this.popup.on('click', 'li', this.options.onSelect);
        }

        if (this.options.returnOnSelect) {
            this.popup.on('click', 'li', function () {
                dialog.return();
            });
        } else {
            this.popup.on('click', 'li', function () {
                if (!$(this).hasClass('selected')) {
                    if (!$(this).closest('ul').data('multiselect')) {
                        $(this).parent('ul').find('li').removeClass('selected');
                    }
                    $(this).addClass('selected');
                } else {

                    // Don't deselect the item if the itemset is mandatory and there aren't any other items selected
                    if ($(this).data('itemSet') && !($(this).data('itemSet') && $(this).data('itemSet').options.mandatory)
                        || $(this).closest('ul').find('li.selected').length > 1) {
                        $(this).removeClass('selected');
                    }
                }

                if(dialog.options.listFilter) {
                    if ($(this).closest('ul').data('id') === dialog.options.filterListId) {
                        let filterValue = $(this).data('filter-value');
                        let listToFilter = dialog.popup.find('ul[data-id="' + dialog.options.listForFilterId + '"]');
                        if (!$(this).hasClass('selected')) {
                            listToFilter.find('li').show();
                        } else {
                            listToFilter.find('li').hide();
                            listToFilter.find('li[data-filter_value="' + filterValue +'"]').show();
                        }
                    }
                }
            });
        }
    };

    /**
     * Creates the content to be used for the dialog
     * @name OpenEyes.UI.AdderDialog#generateContent
     */
    AdderDialog.prototype.generateContent = function () {
        let dialog = this;
        if (this.options.itemSets) {
            this.selectWrapper = $('<table />', {class: 'select-options'});
            let headers = $('<thead />').appendTo(this.selectWrapper);
            this.selectWrapper.appendTo(this.popup);
            let $container = $('<tbody />');
            $container.appendTo(this.selectWrapper);
            this.$tr = $('<tr />').appendTo($container);

            $(this.options.itemSets).each(function (index, itemSet) {
                let header = (itemSet.options.header) ? itemSet.options.header : '';
                $('<th style="'+ itemSet.options.style + '" data-id="'+itemSet.options.id + '"/>').text(header).appendTo(headers);
                let $td = $('<td />', {style: itemSet.options.style}).appendTo(dialog.$tr);
                let $listContainer = $('<div />', {class: 'flex-layout flex-top flex-left'}).appendTo($td);
                if (itemSet.options.supportSigns) {
                    dialog.generateSigns(itemSet).appendTo($listContainer);
                }
                var $list = dialog.generateItemList(itemSet);
                let $listDiv = $('<div />').appendTo($listContainer);
                $list.appendTo($listDiv);
                if (itemSet.options.splitIntegerNumberColumns) {
                    dialog.generateIntegerColumns(itemSet).appendTo($list);
                }
                if (itemSet.options.supportDecimalValues) {
                    dialog.generateDecimalValues(itemSet).appendTo($listContainer);
                }
            });
        }
    };

    /**
     * Generates the search content for the popup
     * @name OpenEyes.UI.AdderDialog#generateSearch
     */
    AdderDialog.prototype.generateSearch = function () {
        let dialog = this;

        let $td = $('<td />');
        this.searchWrapper = $('<div />', {class: 'flex-layout flex-top flex-left'}).appendTo($td);
        $td.appendTo(this.$tr);

        let $searchInput = $('<input />', {
            class: 'search cols-full js-search-autocomplete',
            placeholder: 'search',
            type: 'text'
        });

        this.searchingSpinnerWrapper = $('<div />', {
            class: 'doing-search',
            style: 'display:none'
        });

        $('<i />', {
            class: 'spinner as-icon',
        }).appendTo(this.searchingSpinnerWrapper);
        this.searchingSpinnerWrapper.append(document.createTextNode("Searching ..."));

        let $filterDiv = $('<div />', {class: 'has-filter'}).appendTo(this.searchWrapper);
        $searchInput.appendTo($filterDiv);
        this.searchingSpinnerWrapper.appendTo($filterDiv);

        $searchInput.on('keyup', function () {
            dialog.runItemSearch($(this).val());
        });

        if (dialog.options.filter) {
            let filterContainer = dialog.popup.find('ul[data-id="' + this.options.filterDataId + '"]');
            filterContainer.on('click', 'li', function () {
                let filterValue = false;
                if (!$(this).hasClass('selected')) {
                    filterContainer.find('li.selected').not(this).removeClass('selected');
                    filterValue = $(this).data('id');
                }
                dialog.runItemSearch(dialog.popup.find('input.search').val(), filterValue);
            });
        }

        this.noSearchResultsWrapper = $('<span />').text('No results found');
        this.noSearchResultsWrapper.appendTo($filterDiv);

        if(dialog.options.booleanSearchFilterEnabled) {
            var $searchFilter = $('<div><label class="inline"><input class="js-searchfilter-check" type="checkbox" /> '+this.options.booleanSearchFilterLabel+'</label> </div>');
            $searchFilter.appendTo($filterDiv);
            this.searchWrapper.find(".js-searchfilter-check").on("click", function(e){
                var text = $searchInput.val();
                dialog.runItemSearch(text);
            });
        }

        this.searchResultList = $('<ul />', {class: 'add-options js-search-results'});
        this.searchResultList.appendTo($filterDiv);
    };

    /**
     * Gets all items that are currently selected in the popup
     * @name OpenEyes.UI.AdderDialog#getSelectedItems
     * @returns {Array} A n array of ids and labels of the selected items
     */
    AdderDialog.prototype.getSelectedItems = function () {
        return this.popup.find('li.selected').map(function () {
            return $(this).data();
        }).get();
    };

    /**
     * Generates the item lists for an item set and returns that list
     * @param {OpenEyes.UI.AdderDialog.ItemSet} itemSet The set of items to generate the list for
     * @returns {jQuery|HTMLElement} The generated HTML list
     */
    AdderDialog.prototype.generateItemList = function (itemSet) {
        let dialog = this;
        let additionalClasses = '';
        if (itemSet.options.multiSelect) {
            additionalClasses += ' multi';
        } else {
            additionalClasses += ' single';
        }
        if (itemSet.options.number) {
            additionalClasses += ' number';
        }
        let $list = $('<ul />', {
            class: 'add-options cols-full' + additionalClasses,
            'data-multiselect': itemSet.options.multiSelect,
            'data-id': itemSet.options.id,
            'data-deselectOnReturn': itemSet.options.deselectOnReturn,
        });

        itemSet.items.forEach(function (item) {

      let dataset = AdderDialog.prototype.constructDataset(item);
      let $listItem = $('<li />', dataset);
      if(typeof item.prepended_markup !== "undefined") {
            $(item.prepended_markup).appendTo($listItem);
        }$('<span />', {class: dialog.options.liClass}).text(item['label']).appendTo($listItem);
      if (item.selected) {
        $listItem.addClass('selected');
      }

            $listItem.data('itemSet', itemSet);
            $listItem.appendTo($list);
        });
        return $list;
    };

    /**
     * Generates signs like '+' and '-' in front of the item
     * @param itemSet
     * @returns {jQuery|HTMLElement}
     */
    AdderDialog.prototype.generateSigns = function (itemSet) {
        let dialog = this;
        let $signContainer = $('<div />');
        let $list = $('<ul />', {class: 'add-options cols-full single required'}).appendTo($signContainer);

        Object.entries(itemSet.options.signs).forEach(([term, sign]) => {
            let $listItem = $('<li />', {'data-addition': sign});
            let $iconWrapper = $('<span />', {class: dialog.options.liClass});

            $('<i />', {class: 'oe-i active ' + term}).appendTo($iconWrapper);
            $iconWrapper.appendTo($listItem);
            $listItem.appendTo($list);
        });
        return $signContainer;
    };

    /**
     * Generates decimal values like '.00' , '.25' , '.50' , '.75' next to the item
     * @param itemSet
     * @returns {jQuery|HTMLElement}
     */
    AdderDialog.prototype.generateDecimalValues = function (itemSet) {
        let dialog = this;
        let $decimalValuesContainer = $('<div />');
        let $list = $('<ul />', {class: 'add-options cols-full single required'}).appendTo($decimalValuesContainer);

        itemSet.options.decimalValues.forEach(decimalValue => {
            let $listItem = $('<li />', {'data-addition': decimalValue});
            let $itemWrapper = $('<span />', {class: dialog.options.liClass}).text(decimalValue);
            $itemWrapper.appendTo($listItem);
            $listItem.appendTo($list);
        });

        return $decimalValuesContainer;
    };

    /**
     * Generate an integer with itemSet.options.splitIntegerNumberColumns digits
     * @param itemSet
     * @returns {jQuery|HTMLElement}
     */
    AdderDialog.prototype.generateIntegerColumns = function (itemSet) {
        let $integerColumnsContainer = $('<div class="lists-layout"/>');
        for (let i = 0; i < itemSet.options.splitIntegerNumberColumns.length; i++) {
            let $divList = $('<div />', {class: "list-wrap"}).appendTo($integerColumnsContainer);
            let $list = $('<ul />', {class: 'number'}).appendTo($divList);
            for (let digit = itemSet.options.splitIntegerNumberColumns[i].min; digit <= itemSet.options.splitIntegerNumberColumns[i].max; digit++) {
                let $listItem = $('<li data-'+itemSet.options.id+'="'+digit+'"/>');
                $listItem.append(digit);
                $listItem.appendTo($list);
            }
        }

        return $integerColumnsContainer;
    };

    /**
     * Positions the popup relative to the given anchor
     * @param {jQuery, HTMLElement} $anchorElement The element to anchor the popup to
     */
    AdderDialog.prototype.positionFixedPopup = function ($anchorElement) {
        let dialog = this;

        // js vanilla:
        let btnPos = $anchorElement.get(0).getBoundingClientRect();
        let w = document.documentElement.clientWidth;
        let h = document.documentElement.clientHeight;
        let right = (w - btnPos.right);
        let bottom = (h - btnPos.bottom);

        if (h - bottom < 310) {
            bottom = h - 335;
        }

        // set CSS Fixed position
        this.popup.css({
            bottom: bottom,
            right: right
        });

        if (this.popup.offset().top < 0) {
            this.popup.css({"bottom": Math.floor(bottom + this.popup.offset().top)});
        }

        /*
        Close popup on...
        as scroll event fires on assignment.
        check against scroll position
        */
        let scrollPos = $('.main-event').scrollTop();
        $(this).on('scroll', function () {
            if (scrollPos !== $(this).scrollTop()) {
                // Remove scroll event:
                $(".main-event").off("scroll");
                dialog.close();
            }
        });
    };

    /**
     * Opens (shows) the dialog.
     * @name OpenEyes.UI.AdderDialog#open
     */
    AdderDialog.prototype.open = function () {
        this.isOpen = true;
        this.popup.show();
        let lists = this.popup.find('ul');
        $(lists).each(function () {
            let defaultItem = $(this).find('li[data-set-default="true"]').get(0);
            if (defaultItem) {
                defaultItem.scrollIntoView();
            }
        });

        this.positionFixedPopup(this.options.openButton);
        if (this.options.createBlackoutDiv) {
            this.createBlackoutBox();
        }
        this.positionFixedPopup(this.options.openButton);
        if (this.options.onOpen) {
            this.options.onOpen(this);
        }
    };

    /**
     * Closes (hides) the dialog.
     * @name OpenEyes.UI.AdderDialog#close
     */
    AdderDialog.prototype.close = function () {
        this.isOpen = false;
        this.popup.hide();

        if (this.blackoutDiv) {
            this.blackoutDiv.remove();
            this.blackoutDiv = null;
        }

        if (this.options.onClose) {
            this.options.onClose(this);
        }
    };

    /**
     * Sets which button will be used to close the popup
     * @param {jQuery|HTMLElement} closeButton
     */
    AdderDialog.prototype.setCloseButton = function (closeButton) {
        let dialog = this;
        closeButton.click(function () {
            dialog.close();
        });
    };

    /**
     * Sets which button will be used to open the popup
     * @param {jQuery|HTMLElement} openButton
     */
    AdderDialog.prototype.setOpenButton = function (openButton) {
        let dialog = this;
        openButton.click(function () {
            dialog.open();
            return false;
        });
    };

    /**
     * Sets which button will be used to add items in the popup
     * @param {jQuery|HTMLElement} $addButton
     */
    AdderDialog.prototype.setAddButton = function ($addButton) {

        let dialog = this;

        $addButton.click(function () {
            dialog.return();
        });
    };

    /**
     * Given an object set of attributes, construct it in the format can be used as html element's dataset.
     * @param Object item
     * @returns Object
     */
    AdderDialog.prototype.constructDataset = function (item) {
        let dataset = {};
        if (typeof item === 'string') {
            dataset['data-label'] = item;
        } else {
            for (let key in item) {
                dataset['data-' + key] = item[key];
            }
        }
        return dataset;
    };

    AdderDialog.prototype.return = function () {
        let shouldClose = true;
        if (this.options.onReturn) {
            let selectedValues = [];
            let selectedAdditions = [];
            this.getSelectedItems().forEach(selectedItem => {
                if (selectedItem.addition) {
                    selectedAdditions.push(selectedItem);
                } else {
                    selectedValues.push(selectedItem);
                }
            });
            shouldClose = this.options.onReturn(this, selectedValues, selectedAdditions) !== false;
        }

        if (shouldClose) {
            if (this.options.deselectOnReturn) {
                let itemSets = this.popup.find('ul');
                itemSets.each(function () {
                    let deselect = $(this).data('deselectonreturn');
                    if (typeof deselect === "undefined" || deselect) {
                        $(this).find('li').removeClass('selected');
                    }
                });
            }
            this.close();
        }
    };

    AdderDialog.prototype.toggleColumnById = function(ids, show) {
        let popup = this.popup;
        ids.forEach(function (id) {
            popup.find('th[data-id="'+id+'"]').toggle(show);
            popup.find('[data-id="'+id+'"]').closest('td').toggle(show);
        });
    };

    AdderDialog.prototype.removeSelectedColumnById = function(ids) {
        let popup = this.popup;
        ids.forEach(function (id) {
            popup.find('[data-id="'+id+'"] .selected').removeClass('selected');
        });
    };

    /**
     * Performs a search using the given text
     * @param {string} text The term to search with
     */
    AdderDialog.prototype.runItemSearch = function (text, filterValue) {
        let dialog = this;
        if (this.searchRequest !== null) {
            this.searchRequest.abort();
        }
        if (typeof filterValue === "undefined" && this.options.filter) {
            let selectedFilter = this.popup.find('ul[data-id="' + this.options.filterDataId + '"]').find('li.selected');
            filterValue = selectedFilter.data('id');
        }

        dialog.searchingSpinnerWrapper.show();

        var ajaxOptions = {
            term: text,
            filter: filterValue,
            code: this.options.searchOptions.code,
            ajax: 'ajax'
        };

        if(this.options.booleanSearchFilterEnabled) {
            var filter_on = this.searchWrapper.find(".js-searchfilter-check").prop("checked");
            ajaxOptions[this.options.booleanSearchFilterURLparam] = filter_on ? 1 : 0;
        }

        this.searchRequest = $.getJSON(this.options.searchOptions.searchSource,
            ajaxOptions, function (results) {
            dialog.searchRequest = null;
            let no_data = !$(results).length;

            dialog.searchResultList.empty();
            dialog.noSearchResultsWrapper.text('No results: "' + text + '"');
            dialog.noSearchResultsWrapper.toggle(no_data);

            if (dialog.options.searchOptions.resultsFilter) {
                results = dialog.options.searchOptions.resultsFilter(results);
            }

            $(results).each(function (index, result) {
                var dataset = AdderDialog.prototype.constructDataset(result);
                var $listItem = $("<li />", dataset);
                if(typeof result.prepended_markup !== "undefined") {
                    $(result.prepended_markup).appendTo($listItem);
                }
                $('<span />', {class: 'auto-width'}).text(dataset['data-label']).appendTo($listItem);
                dialog.searchResultList.append($listItem);
            });

            if (dialog.options.enableCustomSearchEntries) {
                dialog.appendCustomEntryOption(text, dialog);
            } else {
                dialog.searchResultList.toggle(!no_data);
            }
            dialog.searchingSpinnerWrapper.hide();
        });
    };

    AdderDialog.prototype.appendCustomEntryOption = function (text, dialog) {
        let new_entry_data = $.extend({
            label: text,
            type: 'custom'
        }, dialog.options.searchAsTypedItemProperties);
        let custom_entry = AdderDialog.prototype.constructDataset(new_entry_data);
        let item = $("<li />", custom_entry).text(dialog.options.searchAsTypedPrefix)
            .append($('<span />', {class: 'auto-width'}).text(text));

        dialog.searchResultList.append(item);
    };

    /**
     * Creates a "blackout div", a mask behind the popup that will close teh dialog if the user clicks anywhere else on the screen
     */
    AdderDialog.prototype.createBlackoutBox = function () {
        let dialog = this;

        this.blackoutDiv = $('<div />', {
            id: 'blackout-div',
            style: 'height: 100%; width: 100%; position: absolute;'
        }).appendTo($('body'));

        this.blackoutDiv.css('z-index', this.popup.css('z-index') - 1);
        this.blackoutDiv.on('click', function () {
            dialog.close();
        });
    };

    exports.AdderDialog = AdderDialog;

}(OpenEyes.UI, OpenEyes.Util, OpenEyes.Util.EventEmitter));
