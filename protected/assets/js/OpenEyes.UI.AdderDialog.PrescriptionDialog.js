(function (exports, Util) {

    var AdderDialog = exports;

    function PrescriptionDialog(options) {
        options = $.extend(true, {}, PrescriptionDialog._defaultOptions, options);

        AdderDialog.call(this, options);
    }

    Util.inherits(AdderDialog, PrescriptionDialog);

    PrescriptionDialog._defaultOptions = {};

    PrescriptionDialog.prototype.create = function () {
        let dialog = this;
        PrescriptionDialog._super.prototype.create.call(this);

        let $header = this.popup.find('.select-options thead');
        $('<th>Filter</th>').appendTo($header);
        $('<th>Preservative</th>').appendTo($header);
        $('<th id="common-drugs-label">Common Drugs</th>').appendTo($header);

        this.popup.on('click', '.js-drug-types li', function () {
            dialog.popup.find('.js-drug-types li.selected').not(this).removeClass('selected');
            dialog.runItemSearch(dialog.popup.find('input.search').val());
        });
        this.popup.on('click', '.js-no-preservative li', function () {
            dialog.runItemSearch(dialog.popup.find('input.search').val());
        });
    };

    PrescriptionDialog.prototype.getSelectedItems = function () {
        return this.popup.find('li.selected').filter(function () {
            return $(this).closest('.js-drug-types').length === 0 &&
                $(this).closest('.js-no-preservative').length === 0;
        }).map(function () {
            return $(this).data();
        }).get();
    };

    PrescriptionDialog.prototype.generateContent = function () {
        let dialog = this;
        if (this.options.itemSets) {
            this.selectWrapper = $('<table />', {class: 'select-options'});
            $('<thead />').appendTo(this.selectWrapper);
            this.selectWrapper.appendTo(this.popup);
            let $container = $('<tbody />');
            $container.appendTo(this.selectWrapper);
            this.$tr = $('<tr />').appendTo($container);

            $(this.options.itemSets).each(function (index, itemSet) {
                let $td = $('<td />').appendTo(dialog.$tr);
                let $listContainer = $('<div />', {class: 'flex-layout flex-top flex-left'}).appendTo($td);
                let $list = dialog.generateItemList(itemSet);
                $list.addClass(itemSet.options.class);
        let $listDiv = $('<div />', {class: 'has-filter'}).appendTo($listContainer);

                // add the search field only to the common_drugs section
                if (itemSet.options.class !== null && itemSet.options.class === "js-drug-list") {
                    let $searchInput = $('<input />', {
                        class: 'search cols-full js-search-autocomplete',
                        placeholder: 'Search...',
                        type: 'text'
                    });
                    $searchInput.appendTo($listDiv);
                    $searchInput.on('keyup', function () {
                        dialog.runItemSearch($(this).val());
                    });
                }

                $list.appendTo($listDiv);
            });
        }
    };

    PrescriptionDialog.prototype.generateSearch = function () {
        let $td = $('<td />');
        this.searchWrapper = $('<div />', {class: 'flex-layout flex-top flex-left'}).appendTo($td);
        $td.prependTo(this.$tr);

    let $filterDiv = $('<div />').appendTo(this.searchWrapper);

        this.noSearchResultsWrapper = $('<span />').text('No results found').hide();
        this.noSearchResultsWrapper.insertAfter(this.popup.find('.js-drug-list'));

    this.searchResultList = $('<ul />', {class: 'add-options js-search-results', style: "display: none;"});
        this.searchResultList.insertAfter(this.popup.find('.js-drug-list'));

        let $drugTypes = $('<ul >', {class: 'add-options js-drug-types'});
        this.options.searchOptions.searchFilter.forEach(function (drugType) {
            $drugTypes.append($('<li />', {'data-id': drugType.id}).append($('<span />', {class: 'auto-width'}).text(drugType.label)));
        });

        $drugTypes.appendTo($filterDiv);
    };

    PrescriptionDialog.prototype.runItemSearch = function (text) {
        let dialog = this;
        if (this.searchRequest !== null) {
            this.searchRequest.abort();
        }

        // Only run the search if the no_preservative button is pressed, the user has entered text into the search field or has a drug type selected
        let doSearch = dialog.popup.find('.js-no-preservative').find('.selected').length === 1 ||
            text.length !== 0 ||
            this.popup.find('.js-drug-types li.selected').length > 0;
        // Otherwise just show the default drug list
        this.popup.find('.js-drug-list').toggle(!doSearch);
        this.popup.find('#common-drugs-label').text(doSearch ? 'Drugs' : 'Common Drugs');

        if (doSearch) {
            let params = $.param({
                term: text,
                code: this.options.searchOptions.code,
                type_id: this.popup.find('.js-drug-types li.selected').data('id'),
                preservative_free: this.popup.find('.js-no-preservative li.selected').data('id'),
                ajax: 'ajax'
            });

            this.searchRequest = $.getJSON(this.options.searchOptions.searchSource + '?' + params, function (results) {
                dialog.searchRequest = null;
                var no_data = !$(results).length;

                dialog.searchResultList.empty();
                dialog.searchResultList.toggle(!no_data);
                dialog.noSearchResultsWrapper.toggle(no_data);

                $(results).each(function (index, result) {
                    let dataset = AdderDialog.prototype.constructDataset(result);
                    let item = $("<li />", dataset);
                    item.append(dataset['data-prepended_markup'], $('<span />', {class: 'auto-width'}).text(dataset['data-label']));

                    dialog.searchResultList.append(item);
                });

                dialog.positionFixedPopup(dialog.options.openButton);
            });
        } else {
            dialog.noSearchResultsWrapper.hide();
            dialog.searchResultList.hide();
        }
    };


    exports.PrescriptionDialog = PrescriptionDialog;

}(OpenEyes.UI.AdderDialog, OpenEyes.Util));