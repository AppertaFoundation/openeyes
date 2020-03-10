(function (exports, Util) {

    var AdderDialog = exports;

    function QuerySearchDialog(options) {
        options = $.extend(true, {}, QuerySearchDialog._defaultOptions, options);

        AdderDialog.call(this, options);
    }

    Util.inherits(AdderDialog, QuerySearchDialog);

    QuerySearchDialog._defaultOptions = {};

    QuerySearchDialog.prototype.create = function () {
        let dialog = this;
        QuerySearchDialog._super.prototype.create.call(this);

        this.popup.on('click', 'ul[data-id="param-type-list"] li', function () {
            $(this).data('selected', 'true');
            $('.js-operators').closest('td').remove();
            $('.js-search-results').closest('td').remove();
            $('.js-digits0').closest('td').remove();
            $('.js-digits1').closest('td').remove();
            $('.js-extra-options').closest('td').remove();
            let type = $(this).data('type');
            $.ajax({
                url: '/OECaseSearch/caseSearch/getOptions?type=' + type,
                success: function(response) {
                    let options = JSON.parse(response);
                    dialog.options.itemSets.splice(1, dialog.options.itemSets.length - 1);
                    dialog.generateOperatorList(options.operations);
                    switch (options.value_type) {
                        case 'number':
                            dialog.generateDigits();
                            break;
                        case 'string_search':
                            /*dialog.options.itemSets.push(
                                new OpenEyes.UI.AdderDialog.ItemSet(
                                    options.common_terms,
                                    {'multiSelect': false, 'id': 'param-common-search-list', 'deSelectOnReturn': true,}
                                )
                            );*/
                            // Add item set for the common searches, then show the search field.
                            dialog.generateSearch(type);
                            break;
                        case 'multi_select':
                            dialog.generateOptionLists(options.option_data);
                            break;
                        default:
                            // Show the search field.
                            dialog.generateSearch(type);
                            break;
                    }
                },
                error: function() {
                    new OpenEyes.UI.Dialog.Alert({
                        content: 'Unable to retrieve options for selected parameter.'
                    }).open();
                }
            });
        });

        /*this.popup.on('click', '.js-drug-types li', function () {
            if ($(this).data('selected') === "none") {
                dialog.popup.find('.js-drug-types li.selected').not(this).removeClass('selected');
                dialog.popup.find('.js-drug-types li').not(this).data('selected', 'none');
                $(this).data('selected', 'true');
            } else {
                $(this).data('selected', 'none');
                $(this).removeClass('selected');
            }
            dialog.runItemSearch(dialog.popup.find('input.search').val());
        });

        this.popup.on('click', '.js-no-preservative li', function () {
            dialog.runItemSearch(dialog.popup.find('input.search').val());
        });*/
    };

    QuerySearchDialog.prototype.getSelectedItems = function () {
        return this.popup.find('li.selected').filter(function () {
            return $(this).closest('.js-drug-types').length === 0 && !$(this).parent().hasClass('js-no-preservative');
        }).map(function () {
            return $(this).data();
        }).get();
    };

    QuerySearchDialog.prototype.generateContent = function () {
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
                let $listContainer = $('<div />', {class: 'lists-layout'}).appendTo($td);
                let $list = dialog.generateItemList(itemSet);
                $list.addClass(itemSet.options.class);
                let $listDiv = $('<div />', {class: 'list-wrap'}).appendTo($listContainer);

                // add the search field only to the common_drugs section
                if (itemSet.options.class !== null && itemSet.options.class === "js-search-results") {
                    /*let $searchInput = $('<input />', {
                        class: 'search cols-full js-search-autocomplete',
                        placeholder: 'Search...',
                        type: 'text'
                    });
                    $searchInput.appendTo($listDiv);
                    $searchInput.on('keyup', function () {
                        dialog.runItemSearch($(this).val());
                    });
                    $listDiv.addClass('has-filter');*/
                }

                $list.appendTo($listDiv);
            });
            $container.on('keyup', '.search-lookup', function () {
                dialog.runItemSearch($(this).val(), $(this).data('type'));
            });
        }
    };

    QuerySearchDialog.prototype.generateOptionLists = function (options) {
        let dialog = this;
        $.each(options, function(index, optionData) {
            // Add the list of operators.
            let $td = $('<td />');
            dialog.optionWrapper = $('<div />', {class: 'lists-layout'}).appendTo($td);
            $td.appendTo(dialog.$tr);

            let $filterDiv = $('<div />', {class: 'list-wrap'}).appendTo(dialog.optionWrapper);

            dialog.optionList = $('<ul />', {
                class: 'add-options single js-extra-options',
                "data-multiselect": "false",
                "data-option-list-id": index
            });

            optionData.options.forEach(function (option) {
                dialog.optionList.append($('<li />', {
                    'data-id': option.id,
                }).append($('<span />', {class: 'auto-width'}).text(option.label)));
            });

            dialog.optionList.appendTo($filterDiv);
        });
    };

    QuerySearchDialog.prototype.generateOperatorList = function(operators) {
        let dialog = this;
        // Add the list of operators.
        let $td = $('<td />');
        this.operationWrapper = $('<div />', {class: 'lists-layout'}).appendTo($td);
        $td.appendTo(this.$tr);

        let $filterDiv = $('<div />', {class: 'list-wrap'}).appendTo(this.operationWrapper);

        this.operatorList = $('<ul />', {
            class: 'add-options single js-operators',
            "data-multiselect": "false"
        });

        operators.forEach(function (operator) {
            dialog.operatorList.append($('<li />', {
                'data-id': operator.id,
                'data-type': 'operator',
            }).append($('<span />', {class: 'auto-width'}).text(operator.label)));
        });

        this.operatorList.appendTo($filterDiv);
    };

    QuerySearchDialog.prototype.generateDigits = function() {
        // Add two rows of 0-9 buttons.
        let dialog = this;
        for (let i = 0; i < 2; i++) {
            // Add the list of operators.
            let $td = $('<td />');
            this.digitWrapper = $('<div />', {class: 'lists-layout'}).appendTo($td);
            $td.appendTo(this.$tr);

            let $filterDiv = $('<div />', {class: 'list-wrap optional-list'}).appendTo(this.digitWrapper);

            this.digitList = $('<ul />', {
                class: 'add-options number single js-digits' + i,
                "data-multiselect": "false"
            });
            [0, 1, 2, 3, 4, 5, 6, 7, 8, 9].forEach(function (digit) {
                dialog.digitList.append($('<li />', {
                    'data-id': digit,
                    'class': (digit === 0 && i === 0) ? 'selected' : '',
                    'data-type': 'number',
                    'data-digit-position': i
                }).append($('<span />', {class: 'auto-width'}).text(digit)));
            });
            this.digitList.appendTo($filterDiv);
        }
    };

    QuerySearchDialog.prototype.generateSearch = function (type, option_data = null) {
        let $td = $('<td />');
        this.searchWrapper = $('<div />', {class: 'lists-layout'}).appendTo($td);
        $td.appendTo(this.$tr);

        let $filterDiv = $('<div />', {class: 'list-wrap optional-list has-filter'}).appendTo(this.searchWrapper);

        this.noSearchResultsWrapper = $('<input />', {
            class: 'search search-lookup cols-full',
            placeholder: 'Search...',
            type: 'text',
            'data-type': type
        });
        this.noSearchResultsWrapper.insertAfter('<span>No results found</span>');
        this.noSearchResultsWrapper.appendTo($filterDiv);

        this.searchResultList = $('<ul />', {
            class: 'add-options single js-search-results',
            "data-multiselect": "false"
        });
        this.searchResultList.appendTo($filterDiv);
    };

    QuerySearchDialog.prototype.runItemSearch = function (text, type) {
        // Add search code here.
        if (!text) {
            $('.js-search-results').empty();
            return;
        }
        this.handleSearch('/OECaseSearch/caseSearch/searchCommonItems?term=' + text + '&type=' + type);
    };

    QuerySearchDialog.prototype.handleSearch = function (url) {
        if (this.searchRequest !== null) {
            this.searchRequest.abort();
        }
        this.searchRequest = $.getJSON(url, null, function(response) {
            $('.js-search-results').empty();
            $.each(response, function(index, item) {
                let $listItem = $('<li />', {
                    "data-id": item.id
                }).append('<span class="auto-width">' + item.label + '</span>');
                $('.js-search-results').append($listItem);
            });
        });
    };

    exports.QuerySearchDialog = QuerySearchDialog;

}(OpenEyes.UI.AdderDialog, OpenEyes.Util));
