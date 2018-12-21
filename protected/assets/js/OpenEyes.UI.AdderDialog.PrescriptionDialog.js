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
    $('<th id="common-drugs-label">Common Drugs</th>').appendTo($header);

    this.popup.on('click', '.js-drug-types li', function () {
      dialog.popup.find('li.selected').not(this).removeClass('selected');
      dialog.runItemSearch(dialog.popup.find('input.search').text());
    });
  };

  PrescriptionDialog.prototype.getSelectedItems = function () {
    return this.popup.find('li.selected').filter(function () {
      return $(this).closest('.js-drug-types').length === 0;
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
        let $listContainer = $('<div />', {class: 'js-drug-list flex-layout flex-top flex-left'}).appendTo($td);
        let $list = dialog.generateItemList(itemSet);
        let $listDiv = $('<div />').appendTo($listContainer);
        $list.appendTo($listDiv);
      });
    }
  };

  PrescriptionDialog.prototype.generateSearch = function () {
    let dialog = this;

    let $td = $('<td />');
    this.searchWrapper = $('<div />', {class: 'flex-layout flex-top flex-left'}).appendTo($td);
    $td.prependTo(this.$tr);

    let $searchInput = $('<input />', {
      class: 'search cols-full js-search-autocomplete',
      placeholder: 'Search...',
      type: 'text'
    });
    let $filterDiv = $('<div />', {class: 'has-filter'}).appendTo(this.searchWrapper);
    $searchInput.appendTo($filterDiv);

    $searchInput.on('keyup', function () {
      dialog.runItemSearch($(this).val());
    });

    this.noSearchResultsWrapper = $('<span />').text('No results found').hide();
    this.noSearchResultsWrapper.insertAfter(this.popup.find('.js-drug-list'));

    this.searchResultList = $('<ul />', {class: 'add-options js-search-results'});
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

    // Only run the search if the user has entered text into the search field or has a drug type selected
    let doSearch = text.length !== 0 || this.popup.find('.js-drug-types li.selected').length > 0;
    // Otherwise just show the default drug list
    this.popup.find('.js-drug-list').toggle(!doSearch);
    this.popup.find('#common-drugs-label').text(doSearch ? 'Drugs' : 'Common Drugs');

    if (doSearch) {
      this.searchRequest = $.getJSON(this.options.searchOptions.searchSource, {
        term: text,
        preservative_free: 0,
        type_id: this.popup.find('.js-drug-types li.selected').data('id'),
        ajax: 'ajax'
      }, function (results) {
        dialog.searchRequest = null;
        var no_data = !$(results).length;

        dialog.searchResultList.empty();
        dialog.searchResultList.toggle(!no_data);
        dialog.noSearchResultsWrapper.toggle(no_data);

        $(results).each(function (index, result) {
          let dataset = AdderDialog.prototype.constructDataset(result);
          let item = $("<li />", dataset)
            .append($('<span />', {class: 'auto-width'}).text(dataset['data-label']));
          dialog.searchResultList.append(item);
        });
      });
    } else {
      dialog.noSearchResultsWrapper.hide();
      dialog.searchResultList.hide();
    }
  };


  exports.PrescriptionDialog = PrescriptionDialog;

}(OpenEyes.UI.AdderDialog, OpenEyes.Util));