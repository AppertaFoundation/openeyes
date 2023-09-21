(function(exports, Util, EventEmitter) {

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
     * @property {popupAnchor} - The DOM handle for positioning the popup (defaults to openButton)
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
        popupAnchor: null,
        onOpen: null,
        onClose: null,
        onSelect: null,
        onReturn: null,
        returnOnSelect: false,
        deselectOnReturn: true,
        id: null,
        parentContainer: null,
        popupClass: 'oe-add-select-search auto-width',
        liClass: 'auto-width',
        searchOptions: null,
        width: null,
        createBlackoutDiv: true,
        enableCustomSearchEntries: false,
        enableCustomEntryWhenNoResults: false,
        searchAsTypedPrefix: 'As typed: ',
        searchAsTypedItemProperties: {},
        filter: false,
        filterDataId: "",
        listFilter: false,
        filterListId: "",
        listForFilterId: "",
        booleanSearchFilterEnabled: false,
        booleanSearchFilterLabel: '',
        booleanSearchFilterURLparam: '',
        showEmptyItemSets: false,
    };

    /**
     * Creates and stores the adder dialog container
     * @name OpenEyes.UI.AdderDialog#create
     */
    AdderDialog.prototype.create = function() {
        let dialog = this;

        let content = $('<div />', { class: this.options.popupClass, id: this.options.id, 'data-test': 'adder-dialog' });
        if (this.options.width) {
            content.css('width', this.options.width);
        }
        let $closeButton = $('<div />', { class: 'close-icon-btn' })
            .append($('<i />', { class: 'oe-i remove-circle medium pro-theme selected' }));
        content.append($closeButton);

        let $addButton = $('<div />', {
            class: 'add-icon-btn',
            'data-test': 'add-icon-btn'
        }).append($('<i />', { class: 'oe-i plus pad pro-theme selected' }));
        $addButton.append('Click to add');


        content.append($addButton);

        if (this.options.openButton) {
            this.setOpenButton(this.options.openButton);
        }
        this.setCloseButton($(content).find('.close-icon-btn'));
        this.setAddButton($addButton);

        if (this.options.parentContainer) {
            content.appendTo($(this.options.parentContainer));
        } else {
            const anchor = this.options.popupAnchor ? this.options.popupAnchor : this.options.openButton;
            content.insertAfter($(anchor));
        }

        this.popup = content;
        this.generateContent();

        if (this.options.searchOptions) {
            this.generateSearch();
        }

        this.popup.hide();

        if (this.options.onSelect) {
            this.popup.on('click', 'li', this.options.onSelect);
        }

        // This is happening in the wrong order for when a selection is made
        // ideally we'd have a series of events, but we ain't there. Can either
        // inherit this and clean it up, or we can tack on a new event after the
        // dialog is created based on the item sets ... maybe find the dependent columns
        // and create a dynamic callback on the event there ...
        if (this.options.returnOnSelect) {
            this.popup.on('click', 'li', function() {
                dialog.return();
            });
        } else {
            this.popup.on('click', 'li', function() {
                if (!$(this).hasClass('selected')) {
                    if (!$(this).closest('ul').data('multiselect')) {
                        $(this).parent('ul').find('li').removeClass('selected');
                    }
                    $(this).addClass('selected');
                } else {
                    // Don't deselect the item if the itemset is mandatory and there aren't any other items selected
                    if (!$(this).data('itemSet') || !($(this).data('itemSet') && $(this).data('itemSet').options.mandatory) ||
                        $(this).closest('ul').find('li.selected').length > 1) {
                        $(this).removeClass('selected');
                    }
                }

                if (dialog.options.listFilter) {
                    if ($(this).closest('ul').data('id') === dialog.options.filterListId) {
                        let filterValue = $(this).data('filter-value');
                        // FIXME: deal with weird inconsistency on data attributes
                        if (filterValue === undefined) {
                            filterValue = $(this).data('filter_value');
                        }
                        let listToFilter = dialog.popup.find('ul[data-id="' + dialog.options.listForFilterId + '"]');
                        if (!$(this).hasClass('selected')) {
                            listToFilter.find('li:not(".js-already-used")').show();
                        } else {
                            listToFilter.find('li').hide().removeClass('selected');
                            listToFilter.find('li[data-filter_value="' + filterValue + '"]:not(".js-already-used")').show();
                        }
                        // default item selection for list filtering
                        let defaultItem = $(listToFilter).find('li[data-set-default="true"]:visible').get(0);
                        if (defaultItem) {
                            defaultItem.scrollIntoView();
                        }
                    }
                }
                // native event
                $(this).closest('ul')[0].dispatchEvent(new Event('adder-change'));
            });
        }
        this.addConditionalLogicListener();
    };

    AdderDialog.prototype.addConditionalLogicListener = function() {
        let adder = this;
        $('#' + this.options.id + ' [data-conditional-id]').click(function() {
            let itemSetId = $(this).parent().data('id');
            let conditionalId = $(this).data('conditional-id');

            for (let itemSet of adder.options.itemSets) {
                if (itemSet.options.id === itemSetId) {
                    for (let map of itemSet.options.conditionalFlowMaps[conditionalId]) {
                        let targetGroup = map['target-group'];
                        let targetId = map['target-id'];
                        $('[id^="' + targetGroup + '"]').parent().css('display', 'none');
                        $('th[data-id^="' + targetGroup + '"]').css('display', 'none');
                        $('#' + targetGroup + '_' + targetId).parent().css('display', '');
                        $('th[data-id="' + targetGroup + '_' + targetId + '"]').css('display', '');
                    }
                }
            }
        });
    }

    /**
     * Creates the content to be used for the dialog
     * @name OpenEyes.UI.AdderDialog#generateContent
     */
    AdderDialog.prototype.generateContent = function() {
        let dialog = this;
        if (this.options.itemSets) {
            this.selectWrapper = $('<table />', { class: 'select-options' });
            dialog.headers = $('<thead />').appendTo(this.selectWrapper);
            this.selectWrapper.appendTo(this.popup);
            let $container = $('<tbody />');
            $container.appendTo(this.selectWrapper);
            this.$tr = $('<tr />').appendTo($container);

            $(this.options.itemSets).each(function(index, itemSet) {
                let header = (itemSet.options.header) ? itemSet.options.header : '';
                let style = itemSet.options.style;

                if (itemSet.options.hideByDefault) {
                    style = style === '' ? 'display: none' : style + '; display: none';
                }

                $('<th style="' + style + '" data-id="' + itemSet.options.id + '" data-test="add-header"/>').text(header).appendTo(dialog.headers);
                let $td = $('<td data-adder-id="' + itemSet.options.id + '" style="' + style + '" />').appendTo(dialog.$tr);
                let $listContainer = $('<div />', { class: 'flex-layout flex-top flex-left', id: itemSet.options.id }).appendTo($td);

                if (itemSet.options.supportSigns) {
                    dialog.generateSigns(itemSet).appendTo($listContainer);
                }
                if (dialog.options.showEmptyItemSets || (itemSet.items && itemSet.items.length)) {
                    let $list = dialog.generateItemList(itemSet);
                    let $listDiv = $('<div />').appendTo($listContainer);
                    $list.appendTo($listDiv);
                }
                if (itemSet.options.splitIntegerNumberColumns) {
                    dialog.generateIntegerColumns(itemSet).appendTo($listContainer);
                }
                if (itemSet.options.generateFloatNumberColumns) {
                    dialog.generateFloatColumns(itemSet).appendTo($listContainer);
                }
                if (itemSet.options.supportDecimalValues) {
                    dialog.generateDecimalValues(itemSet).appendTo($listContainer);
                }
                if (itemSet.options.showInfo) {
                    dialog.generateInfoDisplay(itemSet).appendTo($listContainer);
                }
            });
        }
    };

    /**
     * Generates the search content for the popup
     * @name OpenEyes.UI.AdderDialog#generateSearch
     */
    AdderDialog.prototype.generateSearch = function() {
        let dialog = this;

        let $td = $('<td />');
        this.searchWrapper = $('<div />', { class: 'flex-layout flex-top flex-left' }).appendTo($td);
        $td.appendTo(this.$tr);

        $('<th/>').text("Search").appendTo(dialog.headers);

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

        let $filterDiv = $('<div />', { class: 'has-filter' }).appendTo(this.searchWrapper);
        $searchInput.appendTo($filterDiv);
        this.searchingSpinnerWrapper.appendTo($filterDiv);

        let delaySearch = 0;
        $searchInput.on('keyup', function() {
            let searchInputVal = $(this).val();
            clearTimeout(delaySearch); //stop previous search request

            delaySearch = setTimeout(function() {
                dialog.runItemSearch(searchInputVal);
            }, 500);
        });

        if (dialog.options.filter) {
            let filterContainer = dialog.popup.find('ul[data-id="' + this.options.filterDataId + '"]');
            filterContainer.on('click', 'li', function() {
                let filterValue = false;
                if (!$(this).hasClass('selected')) {
                    filterContainer.find('li.selected').not(this).removeClass('selected');
                    filterValue = $(this).data('id');
                }
                dialog.runItemSearch(dialog.popup.find('input.search').val(), filterValue);
            });
        }

        this.noSearchResultsWrapper = $('<span />', { style: 'display: inherit' }).text('');
        this.noSearchResultsWrapper.appendTo($filterDiv);

        if (dialog.options.booleanSearchFilterEnabled) {

            $('<th/>').text("Search options").appendTo(dialog.headers);
            let $td = $('<td />');
            this.searchOptionsWrapper = $('<div />', { class: 'flex-layout flex-top flex-left' }).appendTo($td);
            $td.appendTo(this.$tr);
            $('<div class="lists-layout">' +
                '<div class="list-wrap ">' +
                '<ul class="add-options cols-full ">' +
                '<li class="js-searchfilter-check" data-label="Include brand names">' +
                '<span class="fixed-width ">Include brand names</span>' +
                '</li></ul></div></div>').appendTo(this.searchOptionsWrapper);



            this.searchOptionsWrapper.find(".js-searchfilter-check").on("click", function(e) {
                var text = $searchInput.val();
                // Have to pass opposite of current value because there is a listener after this that changes
                // the class
                dialog.runItemSearch(text, undefined, !$(this).hasClass('selected'));
            });
        }

        this.searchResultList = $('<ul />', { class: 'add-options js-search-results' });
        this.searchResultList.hide();
        this.searchResultList.appendTo($filterDiv);
    };

    /**
     * Gets all items that are currently selected in the popup
     * @name OpenEyes.UI.AdderDialog#getSelectedItems
     * @returns {Array} A n array of ids and labels of the selected items
     */
    AdderDialog.prototype.getSelectedItems = function() {
        return this.popup.find('li.selected:not(.js-searchfilter-check)').map(function() {
            return $(this).data();
        }).get();
    };

    /**
     * Retrieve values directly from a specific ItemSet
     *
     * @param itemSet
     * @return {string|Filtered}
     */
    AdderDialog.prototype.getSelectedItemsForItemSet = function(itemSet) {
        if (itemSet.options.generateFloatNumberColumns) {
            return this._parseNumberFromSelection(itemSet)[1];
        }
        return this.getSelectedItems().filter(function(item) {
            return item.itemSet && item.itemSet === itemSet;
        }).map(function(item) {
            return item.id;
        });
    };

    AdderDialog.prototype.setSelectedItemsForItemSet = function(itemSet, value) {
        this.clearSelectionForItemSet(itemSet);
        if (itemSet.options.generateFloatNumberColumns) {
            this._setNumberSelection(itemSet, value);
        } else {
            if (!Array.isArray(value)) {
                value = [value];
            }
            let $ul = this.popup.find('ul[data-id="' + itemSet.options.id + '"]');
            value.forEach(function(itemValue) {
                $ul.find('li[data-id="' + itemValue + '"]').addClass('selected').data('selected');
            });
            $ul[0].dispatchEvent(new Event('adder-change'));
        }

    };

    /**
     * Generates the item lists for an item set and returns that list
     * @param {OpenEyes.UI.AdderDialog.ItemSet} itemSet The set of items to generate the list for
     * @returns {jQuery|HTMLElement} The generated HTML list
     */
    AdderDialog.prototype.generateItemList = function(itemSet) {
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
        if (dialog.options.listFilter && (itemSet.options.id === dialog.options.filterListId)) {
            additionalClasses += ' category-filter ignore';
        }
        let $list = $('<ul />', {
            class: 'add-options cols-full' + additionalClasses,
            'data-multiselect': itemSet.options.multiSelect,
            'data-id': itemSet.options.id,
            'data-deselectOnReturn': itemSet.options.deselectOnReturn,
            'data-resetSelectionToDefaultOnReturn': itemSet.options.resetSelectionToDefaultOnReturn,
            'data-test': 'add-options',
        });

        itemSet.items.forEach(function(item) {

            let dataset = AdderDialog.prototype.constructDataset(item);
            let $listItem = $('<li />', dataset);
            $('<span />', { class: dialog.options.liClass }).text(item['label']).appendTo($listItem);
            if (typeof item.prepended_markup !== "undefined") {
                $(item.prepended_markup).appendTo($listItem);
            }
            if (item.selected || item.defaultSelected) {
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
    AdderDialog.prototype.generateSigns = function(itemSet) {
        let dialog = this;
        let $signContainer = $('<div />');
        let $list = $('<ul />', {
            class: 'add-options single signs',
        }).appendTo($signContainer);

        Object.entries(itemSet.options.signs).forEach(([term, sign]) => {
            let $listItem = $('<li />', { 'data-addition': sign, 'data-type': 'sign' });
            let $iconWrapper = $('<span />', { class: dialog.options.liClass });

            $('<i />', { class: 'oe-i active ' + term }).appendTo($iconWrapper);
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
    AdderDialog.prototype.generateDecimalValues = function(itemSet) {
        let dialog = this;
        let $decimalValuesContainer = $('<div />');
        let $list = $('<ul />', { class: 'add-options single required' }).appendTo($decimalValuesContainer);

        itemSet.options.decimalValues.forEach(decimalValue => {
            let $listItem = $('<li />', { 'data-addition': decimalValue, 'data-type': itemSet.options.decimalValuesType });
            let $itemWrapper = $('<span />', { class: dialog.options.liClass }).text(decimalValue);
            $itemWrapper.appendTo($listItem);
            $listItem.appendTo($list);
        });

        return $decimalValuesContainer;
    };

    /**
     * Abstraction for generating picker list of digits
     *
     * @param min
     * @param max
     * @param position
     * @param itemSetId
     * @return {*|jQuery|HTMLElement}
     * @private
     */
    AdderDialog.prototype._generateDigitList = function(min, max, position, itemSetId, beforeDecimalPt) {
        if (beforeDecimalPt == undefined) {
            beforeDecimalPt = true;
        }
        let listClass = 'add-options number ' + (beforeDecimalPt ? 'whole' : 'fraction');
        let $divList = $('<div />', { class: "list-wrap" });
        let $list = $('<ul />', { class: listClass, id: itemSetId + '-number-digit-' + position, 'data-id': itemSetId }).appendTo($divList);
        for (let digit = min; digit <= max; digit++) {
            let $listItem = $('<li data-value="' + digit + '"/>');
            $listItem.append(digit);
            $listItem.appendTo($list);
        }

        return $divList;
    };

    /**
     * Generate an integer with itemSet.options.splitIntegerNumberColumns digits
     *
     * @param itemSet
     * @returns {jQuery|HTMLElement}
     */
    AdderDialog.prototype.generateIntegerColumns = function(itemSet) {
        let $integerColumnsContainer = $('<div class="lists-layout"/>');

        for (let i = 0; i < itemSet.options.splitIntegerNumberColumns.length; i++) {
            let type = itemSet.options.splitIntegerNumberColumns.length === itemSet.options.splitIntegerNumberColumns.length ? 'data-type="' + itemSet.options.splitIntegerNumberColumnsTypes[i] + '"' : '';
            let $divList = $('<div />', { class: "list-wrap" }).appendTo($integerColumnsContainer);
            let $list = $('<ul />', { class: 'add-options number', id: 'number-digit-' + i }).appendTo($divList);
            for (let digit = itemSet.options.splitIntegerNumberColumns[i].min; digit <= itemSet.options.splitIntegerNumberColumns[i].max; digit++) {
                let $listItem = $('<li data-' + itemSet.options.id + '="' + digit + '"' + type + '/>');
                $listItem.append(digit);
                $listItem.appendTo($list);
            }
        }

        return $integerColumnsContainer;
    };

    /**
     * Generate columns of numbers to enter a float as per requirements in itemSet.options.generateFloatNumberColumns
     *
     * @param itemSet
     * @return {*|jQuery|HTMLElement}
     */
    AdderDialog.prototype.generateFloatColumns = function(itemSet) {
        let $floatColumnsContainer = $('<div class="lists-layout number-lists"/>');
        let minValue = parseFloat(itemSet.options.generateFloatNumberColumns.minValue);
        if (isNaN(minValue)) {
            minValue = 0;
        }
        let integerMax = Math.floor(itemSet.options.generateFloatNumberColumns.maxValue);
        let numberOfDigits = integerMax.toString().length;
        let firstMaxDigit = integerMax.toString()[0];

        for (let i = 1; i <= numberOfDigits; i++) {
            let min = (i > 1 || numberOfDigits === 1) ? 0 : 1;
            let max = (i === 1) ? firstMaxDigit : 9;

            this._generateDigitList(min, max, i, itemSet.options.id, true)
                .appendTo($floatColumnsContainer);
        }

        let decimalPlaces = itemSet.options.generateFloatNumberColumns.decimalPlaces || 0;

        if (decimalPlaces > 0) {
            $floatColumnsContainer.append($('<div class="decimal-point ignore">.</div>'));
            for (let i = 1; i <= decimalPlaces; i++) {
                this._generateDigitList(0, 9, i + numberOfDigits, itemSet.options.id, false)
                    .appendTo($floatColumnsContainer);
            }
        }

        return $floatColumnsContainer;
    };

    AdderDialog.prototype._parseNumberFromSelection = function(itemSet) {
        let integerPartSelected = false;
        let fractionalPartNotSelected = false;
        let decimalPtParsed = false;
        let valid = true;
        let numberString = '';
        this.popup.find('[data-adder-id="' + itemSet.options.id + '"]')
            .find('.number-lists')
            .find('div')
            .each(function(index) {
                if ($(this).hasClass('decimal-point')) {
                    decimalPtParsed = true;
                    numberString += '.';
                    return true;
                }

                let selected = $(this).find('li.selected');
                if (selected.length > 0) {
                    numberString += $(selected[0]).data('value');

                    if (decimalPtParsed) {
                        if (fractionalPartNotSelected) {
                            valid = false;
                        }
                    } else {
                        integerPartSelected = true;
                    }
                } else {
                    if (decimalPtParsed) {
                        fractionalPartNotSelected = true;
                    } else {
                        if (integerPartSelected) {
                            valid = false;
                        }
                    }
                }
                return valid;
            });

        if (!decimalPtParsed && itemSet.options.supportDecimalValues) {
            // check for fixed decimal selection
            let selectedDecimal = this.popup.find('[data-adder-id="' + itemSet.options.id + '"] [data-type="' + itemSet.options.decimalValuesType + '"].selected');
            if (selectedDecimal !== undefined) {
                numberString += selectedDecimal.data('addition');
                decimalPtParsed = true;
            }
        }

        const number = decimalPtParsed ? parseFloat(numberString) : parseInt(numberString);

        let signFactor = 1;
        if (itemSet.options.supportSigns) {
            const selectedSign = this.popup.find('[data-adder-id="' + itemSet.options.id + '"]')
                .find('.selected[data-type="sign"]');
            if (selectedSign && selectedSign.data('addition') === '-') {
                signFactor = -1;
            }
        }

        return [valid, isNaN(number) ? undefined : signFactor * number];
    };

    AdderDialog.prototype._setNumberSelection = function(itemSet, value) {
        if (value === undefined || value === null) {
            return;
        }

        let whole, fraction;
        [whole, fraction] = value.split(".");
        this._setWholeNumberSelection(itemSet, whole);
        this._setFractionNumberSelection(itemSet, fraction);
        this._setSignSelection(itemSet, value);
    };

    AdderDialog.prototype._setWholeNumberSelection = function(itemSet, whole) {
        whole = whole.split('').reverse();
        $(this.popup.find('[data-adder-id="' + itemSet.options.id + '"]')
            .find('.whole')
            .get()
            .reverse()
        ).each(function(index, numberList) {
            if (whole[index] !== undefined) {
                $(numberList).find("[data-value='" + whole[index] + "']").addClass('selected');
            }
        });
    };

    AdderDialog.prototype._setFractionNumberSelection = function(itemSet, fraction) {
        if (!fraction) {
            return;
        }

        if (itemSet.options.generateFloatNumberColumns) {
            fraction = fraction.split('');
            this.popup.find('[data-adder-id="' + itemSet.options.id + '"]')
                .find('.fraction')
                .each(function(index, numberList) {
                    if (fraction[index] !== undefined) {
                        $(numberList).find("[data-value='" + fraction[index] + "']").addClass('selected');
                    }
                });
        }

        if (itemSet.options.supportDecimalValues) {
            this.popup.find('[data-adder-id="' + itemSet.options.id + '"]')
                .find('li[data-type="decimalValue"]').removeClass('selected');
            this.popup.find('[data-adder-id="' + itemSet.options.id + '"]')
                .find('li[data-addition=".' + fraction + '"][data-type="decimalValue"]')
                .addClass('selected');
        }
    };

    AdderDialog.prototype._setSignSelection = function(itemSet, value) {
        if (!itemSet.options.supportSigns) {
            return;
        }
        const sign = parseFloat(value) >= 0 ? '+' : '-';
        const itemsList = this.popup
            .find('[data-adder-id="' + itemSet.options.id + '"]')
            .find('.add-options.signs');
        itemsList.not('li[data-addition="' + sign + '"]').removeClass('selected');
        itemsList.find('li[data-addition="' + sign + '"]').addClass('selected');
    };

    AdderDialog.prototype.validateNumberSelection = function(itemSet) {
        let parsedNumber = this._parseNumberFromSelection(itemSet);
        return parsedNumber[0];
    };

    AdderDialog.prototype.getNumberSelection = function(itemSet) {
        let parsedNumber = this._parseNumberFromSelection(itemSet);
        return parsedNumber[1];
    };

    AdderDialog.prototype.generateInfoDisplay = function(itemSet) {
        let $container = $('<div class="lists-layout"/>');
        $container.append('<div class="optional-placeholder">' + itemSet.options.showInfo + '</div>');
        return $container;
    };

    /**
     * Positions the popup relative to the given anchor
     * @param {jQuery, HTMLElement} $anchorElement The element to anchor the popup to
     */
    AdderDialog.prototype.positionFixedPopup = function($anchorElement) {
        let dialog = this;

        // js vanilla:
        let btnPos = $anchorElement.get(0).getBoundingClientRect();
        let w = document.documentElement.clientWidth;
        let h = document.documentElement.clientHeight;
        let right = (w - btnPos.right);
        let bottom = (h - btnPos.bottom);
        let left = btnPos.left;
        let position = 'right';
        if (w - right < 620) {
            right = w - 645;
        }

        if (h - bottom < 310) {
            bottom = h - 335;
        }
        // if the adder popup is fired from a sidebar, change the positioning
        if (this.options.source === 'sidebar') {
            position = 'left';
        }
        let position_dict = {
            'right': right,
            'left': left,
        }
        let position_css = {
            bottom: bottom
        }
        position_css[position] = position_dict[position];
        // set CSS Fixed position
        this.popup.css(position_css);

        if (this.popup.offset().top < 0) {
            this.popup.css({ "bottom": Math.floor(bottom + this.popup.offset().top) });
        }

        if (this.popup.offset().left < 0) {
            this.popup.css({ "right": Math.floor(right + this.popup.offset().left) });
        }

        /*
        Close popup on...
        as scroll event fires on assignment.
        check against scroll position
        */
        let scrollPos = $('.main-event').scrollTop();
        $(this).on('scroll', function() {
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
    AdderDialog.prototype.open = function() {
        this.isOpen = true;
        this.popup.show();
        let lists = this.popup.find('ul');
        $(lists).each(function() {
            let defaultItem = $(this).find('li[data-set-default="true"]').get(0);
            if (defaultItem) {
                defaultItem.scrollIntoView();
            }
        });

        const positionAnchor = this.options.popupAnchor ? this.options.popupAnchor : this.options.openButton;
        this.positionFixedPopup(positionAnchor);
        if (this.options.createBlackoutDiv) {
            this.createBlackoutBox();
        }
        this.positionFixedPopup(positionAnchor);
        if (this.options.onOpen) {
            this.options.onOpen(this);
        }
    };

    /**
     * Closes (hides) the dialog.
     * @name OpenEyes.UI.AdderDialog#close
     */
    AdderDialog.prototype.close = function() {
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
    AdderDialog.prototype.setCloseButton = function(closeButton) {
        let dialog = this;
        closeButton.click(function() {
            dialog.close();
        });
    };

    /**
     * Sets which button will be used to open the popup
     * @param {jQuery|HTMLElement} openButton
     */
    AdderDialog.prototype.setOpenButton = function(openButton) {
        let dialog = this;
        openButton.addClass('openeyes-ui-adderdialog-open-btn');
        openButton.click(function() {
            dialog.open();
            return false;
        });
    };

    /**
     * Sets which button will be used to add items in the popup
     * @param {jQuery|HTMLElement} $addButton
     */
    AdderDialog.prototype.setAddButton = function($addButton) {

        let dialog = this;

        $addButton.click(function() {
            dialog.return();
        });
    };

    /**
     * Given an object set of attributes, construct it in the format can be used as html element's dataset.
     * @param Object item
     * @returns Object
     */
    AdderDialog.prototype.constructDataset = function(item) {
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

    AdderDialog.prototype.return = function() {
        let shouldClose = true;
        let dialog = this;

        for (let item of this.options.itemSets) {

            let optionCount = $('.add-options[data-id="' + item.options.id + '"]').find('li.selected').length;
            if (item.options.mandatory && optionCount === 0) {
                shouldClose = false;
                new OpenEyes.UI.Dialog.Alert({
                    content: item.options.header + " must have an option selected."
                }).open();
            }
            if (item.options.generateFloatNumberColumns) {
                if (!dialog.validateNumberSelection(item)) {
                    shouldClose = false;
                    new OpenEyes.UI.Dialog.Alert({
                        content: item.options.header + " must be a valid number."
                    }).open();
                } else {
                    let value = dialog.getNumberSelection(item);
                    if (value === "" || value === null) {
                        continue;
                    }

                    if (item.options.generateFloatNumberColumns.minValue && item.options.generateFloatNumberColumns.minValue > value) {
                        shouldClose = false;
                        new OpenEyes.UI.Dialog.Alert({
                            content: item.options.header + " is too small."
                        }).open();
                    }
                    if (item.options.generateFloatNumberColumns.maxValue && item.options.generateFloatNumberColumns.maxValue < value) {
                        shouldClose = false;
                        new OpenEyes.UI.Dialog.Alert({
                            content: item.options.header + " is too large."
                        }).open();
                    }
                }
            }
        }

        if (shouldClose && dialog.options.onReturn) {
            let selectedValues = [];
            let selectedAdditions = [];
            dialog.getSelectedItems().forEach(selectedItem => {
                if (selectedItem.addition) {
                    selectedAdditions.push(selectedItem);
                } else {
                    selectedValues.push(selectedItem);
                }
            });
            shouldClose = dialog.options.onReturn(dialog, selectedValues, selectedAdditions) !== false;
        }

        if (shouldClose) {
            let itemSets = dialog.popup.find('ul');
            if (dialog.options.deselectOnReturn) {
                itemSets.each(function(index, itemSet) {
                    let deselect = $(itemSet).data('deselectonreturn');
                    let reset = $(itemSet).data('resetselectiontodefaultonreturn');
                    if (typeof deselect === "undefined" || deselect || reset) {
                        $(itemSet).find('li').removeClass('selected');
                    }
                });
                // deselect options when closing the adderDialog
                dialog.popup.find('.selected').removeClass('selected');
            }

            itemSets.each(function(itemSetIndex, itemSet) {
                if ($(itemSet).data('resetselectiontodefaultonreturn')) {
                    $(itemSet).find('li').each(function(listIndex, listItem) {
                        let itemData = dialog.options.itemSets[itemSetIndex].items[listIndex];
                        if ('defaultSelected' in itemData && itemData.defaultSelected) {
                            $(listItem).addClass('selected');
                        }
                    });
                }
            });

            const $input = dialog.popup.find('.js-search-autocomplete.search');
            // reset search list when adding an item
            if ($input.length) {
                $input.val("");
                // run item search with empty text so AdderDialogs that extend this class run their custom settings
                this.runItemSearch('');
            }

            dialog.popup[0].dispatchEvent(new Event('adder-reset'));

            this.close();
        }
    };

    AdderDialog.prototype.toggleColumnById = function(ids, show) {
        let popup = this.popup;
        ids.forEach(function(id) {
            popup.find('th[data-id="' + id + '"]').toggle(show);
            popup.find('[data-adder-id="' + id + '"]').toggle(show);
        });
    };

    AdderDialog.prototype.removeSelectedColumnById = function(ids) {
        let popup = this.popup;
        ids.forEach(function(id) {
            popup.find('[data-id="' + id + '"] .selected').removeClass('selected');
        });
    };

    AdderDialog.prototype.clearSelectionForItemSet = function(itemSet) {
        this.popup.find('[data-adder-id="' + itemSet.options.id + '"] .selected').removeClass('selected');
    };

    /**
     * Performs a search using the given text
     * @param {string} text The term to search with
     */
    AdderDialog.prototype.runItemSearch = function(text, filterValue, searchFilterValue) {
        let dialog = this;
        if (this.searchRequest !== null) {
            this.searchRequest.abort();
        }
        if (typeof filterValue === "undefined" && this.options.filter) {
            let selectedFilter = this.popup.find('ul[data-id="' + this.options.filterDataId + '"]').find('li.selected');
            filterValue = selectedFilter.data('id');
        }
        // reset results lists if there is no text searched
        if (!text.length && !filterValue) {
            dialog.searchResultList.empty();
            dialog.noSearchResultsWrapper.text('');
            dialog.noSearchResultsWrapper.toggle(true);
            return;
        }

        dialog.searchingSpinnerWrapper.show();

        var ajaxOptions = {
            term: text,
            filter: filterValue,
            code: this.options.searchOptions.code,
            ajax: 'ajax'
        };

        if (this.options.booleanSearchFilterEnabled) {
            let filter_on;
            if (typeof searchFilterValue === "undefined") {
                filter_on = this.searchOptionsWrapper.find(".js-searchfilter-check").hasClass("selected");
            } else {
                filter_on = searchFilterValue;
            }
            ajaxOptions[this.options.booleanSearchFilterURLparam] = filter_on ? 1 : 0;
        }

        this.searchRequest = $.getJSON(this.options.searchOptions.searchSource,
            ajaxOptions,
            function(results) {
                dialog.searchRequest = null;
                let no_data = !$(results).length;

                dialog.searchResultList.empty();
                dialog.noSearchResultsWrapper.text('No results: "' + text + '"');
                dialog.noSearchResultsWrapper.toggle(no_data);

                if (dialog.options.searchOptions.resultsFilter) {
                    results = dialog.options.searchOptions.resultsFilter(results);
                }

                $(results).each(function(index, result) {
                    var dataset = AdderDialog.prototype.constructDataset(result);
                    var $listItem = $("<li />", dataset);
                    $('<span />', { class: dialog.options.liClass }).text(dataset['data-label']).appendTo($listItem);
                    if (typeof result.prepended_markup !== "undefined") {
                        $(result.prepended_markup).appendTo($listItem);
                    }
                    dialog.searchResultList.append($listItem);
                });

            if (dialog.options.enableCustomEntryWhenNoResults) {
                if (results.length === 0) {
                    dialog.appendCustomEntryOption(text, dialog);
                } else {
                    dialog.searchResultList.show();
                }
            } else if (dialog.options.enableCustomSearchEntries) {
                dialog.appendCustomEntryOption(text, dialog);
                dialog.searchResultList.show();
            } else {
                dialog.searchResultList.toggle(!no_data);
            }

                dialog.searchingSpinnerWrapper.hide();
            });
    };

    AdderDialog.prototype.appendCustomEntryOption = function(text, dialog) {
        let new_entry_data = $.extend({
            label: text,
            type: 'custom'
        }, dialog.options.searchAsTypedItemProperties);
        let custom_entry = AdderDialog.prototype.constructDataset(new_entry_data);
        let item = $("<li />", custom_entry).text(dialog.options.searchAsTypedPrefix)
            .append($('<span />', { class: 'auto-width' }).text(text));

        dialog.searchResultList.append(item);
    };

    /**
     * Creates a "blackout div", a mask behind the popup that will close teh dialog if the user clicks anywhere else on the screen
     */
    AdderDialog.prototype.createBlackoutBox = function() {
        let dialog = this;

        this.blackoutDiv = $('<div />', {
            id: 'blackout-div',
            style: 'height: 100%; width: 100%; position: absolute;'
        }).appendTo($('body'));

        this.blackoutDiv.css('z-index', this.popup.css('z-index') - 1);
        this.blackoutDiv.on('click', function() {
            dialog.close();
        });
    };

    AdderDialog.prototype.remove = function() {
        this.close();
        if (this.blackoutDiv) {
            this.blackoutDiv.remove();
        }
        this.popup.remove();
    };

    exports.AdderDialog = AdderDialog;

}(OpenEyes.UI, OpenEyes.Util, OpenEyes.Util.EventEmitter));