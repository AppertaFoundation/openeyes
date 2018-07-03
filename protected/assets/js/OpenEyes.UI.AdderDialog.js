(function (exports, Util, EventEmitter) {

  'use strict';

  function AdderDialog(options) {

    this.searchRequest = null;

    EventEmitter.call(this);
    this.options = $.extend(true, {}, AdderDialog._defaultOptions, options);
    this.create();
  }

  Util.inherits(EventEmitter, AdderDialog);

  /**
   * The default dialog options. Custom options will be merged with these.
   * @name OpenEyes.UI.Dialog#_defaultOptions
   * @property {mixed} [content=null] - Content to be displayed in the dialog.
   * This option accepts multiple types, including strings, DOM elements, jQuery instances, etc.
   * @property {boolean} [destroyOnClose=true] - Destroy the dialog when it is closed?
   * @property {string|null} [url=null] - A URL string to load the dialog content in via an
   * AJAX request.
   * @property {object|null} [data=null] - Request data used when loading dialog content
   * via an AJAX request.
   * @property {string|null} [iframe=null] - A URL string to load the dialog content
   * in via an iFrame.
   * @property {string|null} [title=null] - The dialog title.
   * @property {string|null} [dialogClass=dialog] - A CSS class string to be added to
   * the main dialog container.
   * @property {boolean} [constrainToViewport=false] - Constrain the dialog dimensions
   * so that it is never displayed outside of the window viewport?
   * @property {integer|string} [width=400] - The dialog width.
   * @property {integer|string} [height=auto] - The dialog height.
   * @private
   */
  AdderDialog._defaultOptions = {
    itemSets: [],
    openButton: null,
    onOpen: null,
    onClose: null,
    onSelect: null,
    onReturn: null,
    onAdd: null,
    deselectOnReturn: true,
    id: null,
    popupClass: 'oe-add-select-search auto-width',
    liClass: 'auto-width',
    searchOptions: null,
  };

  /**
   * Creates and stores the dialog container, and creates a new jQuery UI
   * instance on the container.
   * @name OpenEyes.UI.Dialog#create
   * @method
   * @private
   */
  AdderDialog.prototype.create = function () {
    var dialog = this;

    var content = $('<div />', {class: this.options.popupClass, id: this.options.id});
    var $closeButton = $('<div />', {class: 'close-icon-btn'})
      .append($('<i />', {class: 'oe-i remove-circle medium'}));
    content.append($closeButton);

    var $addButton = $('<button />', {
      class: 'button hint green add-icon-btn',
      type: 'button'
    }).append($('<i />', {class: 'oe-i plus pro-theme'}));

    if (this.options.searchOptions) {
      this.searchWrapper = $('<div />', {class: 'search-options'});
      this.searchWrapper.appendTo(content);
      this.generateSearch();
      if (this.options.itemSets) {
        this.generateMenu(content);
      }
    }

    content.append($addButton);

    this.setOpenButton(this.options.openButton);
    this.setCloseButton($(content).find('.close-icon-btn'));
    this.setAddButton($addButton);

    content.insertAfter(this.options.openButton);
    this.popup = this.options.openButton.siblings('.oe-add-select-search');
    this.generateContent();
    this.popup.hide();

    if (this.options.onSelect) {
      this.popup.on('click', 'li', this.options.onSelect);
    } else {
      this.popup.on('click', 'li', function () {
        if (!$(this).hasClass('selected')) {
          if (!$(this).closest('ul').data('multiselect')) {
            $(this).parent('ul').find('li').removeClass('selected');
          }
          $(this).addClass('selected');
        } else {
          $(this).removeClass('selected');
        }
      });
    }
  };

  AdderDialog.prototype.generateContent = function () {
    var dialog = this;

    console.log(this.options.itemSets);

    if (this.options.itemSets) {
      this.selectWrapper = $('<div />', {class: 'select-options'});
      this.selectWrapper.appendTo(this.popup);
      var $container = $('<div />', {class: 'flex-layout flex-top flex-left'}).appendTo(this.popup);
      $container.appendTo(this.selectWrapper);
      $(this.options.itemSets).each(function (index, itemSet) {
        var $list = dialog.generateItemList(itemSet);
        $list.appendTo($container);
      });
    }
  };

  AdderDialog.prototype.generateMenu = function (content) {
    var dialog = this;

    var $selectButton = $('<div />', {class: 'select-icon-btn'})
      .append($('<i />', {class: 'oe-i menu selected'}));

    var $searchButton = $('<div />', {class: 'search-icon-btn'})
      .append($('<i />', {class: 'oe-i search'}));

    $selectButton.appendTo(content);
    $searchButton.appendTo(content);

    $selectButton.click(function () {
      $(this).find('i').addClass('selected');
      $searchButton.find('i').removeClass('selected');

      dialog.searchWrapper.hide();
      dialog.selectWrapper.show();
      dialog.selectWrapper.find('li').removeClass('selected');
    });

    $searchButton.click(function () {
      $(this).find('i').addClass('selected');
      $selectButton.find('i').removeClass('selected');

      dialog.searchWrapper.show();
      dialog.selectWrapper.hide();
      dialog.searchWrapper.find('li').removeClass('selected');
    });
  };

  AdderDialog.prototype.generateSearch = function () {
    var dialog = this;

    var $searchInput = $('<input />', {
      class: 'search cols-full js-search-autocomplete',
      placeholder: 'search',
      type: 'text'
    });
    $searchInput.appendTo(this.searchWrapper);

    $searchInput.on('keyup', function () {
      dialog.onSearchKeyUp($(this).val());
    });

    this.noSearchResultsWrapper = $('<span />').text('No results found');
    this.noSearchResultsWrapper.appendTo(this.searchWrapper);

    this.searchResultList = $('<ul />', {class: 'add-options js-search-results', style: 'display: none;'});
    this.searchResultList.appendTo(this.searchWrapper);
    this.searchWrapper.hide();
  };

  AdderDialog.prototype.getSelectedItems = function () {
    return this.popup.find('li.selected').map(function () {
      return {'id': $(this).data('id'), 'label': $(this).data('label')};
    }).get();
  };

  AdderDialog.prototype.generateItemList = function (itemSet) {
    var dialog = this;
    var $list = $('<ul />', {class: 'add-options cols-full', 'data-multiselect': itemSet.options.multiSelect});

    itemSet.items.forEach(function (item) {
      var $listItem = $('<li />', {'data-label': item['label'], 'data-id': item['id']});
      $('<span />', {class: dialog.options.liClass}).text(item['label']).appendTo($listItem);
      $listItem.appendTo($list);
    });

    return $list;
  };

  AdderDialog.prototype.positionFixedPopup = function ($anchorElement) {
    var dialog = this;

    // js vanilla:
    var btnPos = $anchorElement.get(0).getBoundingClientRect();
    var w = document.documentElement.clientWidth;
    var h = document.documentElement.clientHeight;
    var right = (w - btnPos.right);
    var bottom = (h - btnPos.bottom);

    // set CSS Fixed position
    this.popup.css({
      bottom: bottom,
      right: right
    });

    /*
    Close popup on...
    as scroll event fires on assignment.
    check against scroll position
    */
    var scrollPos = $('.main-event').scrollTop();
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
   * @name OpenEyes.UI.Dialog#open
   * @method
   * @public
   */
  AdderDialog.prototype.open = function () {
    this.popup.show();
    this.positionFixedPopup(this.options.openButton);
  };

  /**
   * Closes (hides) the dialog.
   * @name OpenEyes.UI.Dialog#close
   * @method
   * @public
   */
  AdderDialog.prototype.close = function () {
    this.popup.hide();
  };

  AdderDialog.prototype.setCloseButton = function (closeButton) {
    var dialog = this;
    closeButton.click(function () {
      dialog.close();
    });
  };

  AdderDialog.prototype.setOpenButton = function (openButton) {
    var dialog = this;
    openButton.click(function () {
      dialog.open();
    });
  };

  AdderDialog.prototype.setAddButton = function ($addButton) {

    var dialog = this;

    $addButton.click(function () {
      if (dialog.options.onReturn) {
        var selectedItems = dialog.getSelectedItems();
        var result = dialog.options.onReturn(dialog, selectedItems);
        if (result) {
          dialog.close();
        }
      } else {
        dialog.close();
      }

      if (dialog.options.deselectOnReturn) {
        dialog.popup.find('li').removeClass('selected');
      }
    });
  };

  /** Event handlers */

  /**
   * Emit the 'open' event after the dialog has opened.
   * @name OpenEyes.UI.Dialog#onDialogOpen
   * @fires OpenEyes.UI.Dialog#open
   * @method
   * @private
   */
  AdderDialog.prototype.onDialogOpen = function () {
    /**
     * Emitted after the dialog has opened.
     *
     * @event OpenEyes.UI.Dialog#open
     */
    this.emit('open');
  };

  /**
   * Emit the 'close' event after the dialog has closed, and optionally destroy
   * the dialog.
   * @name OpenEyes.UI.Dialog#onDialogClose
   * @fires OpenEyes.UI.Dialog#close
   * @method
   * @private
   */
  AdderDialog.prototype.onDialogClose = function () {
    /**
     * Emitted after the dialog has closed.
     *
     * @event OpenEyes.UI.Dialog#close
     */
    this.emit('close');

    if (typeof enableButtons === 'function') {
      enableButtons();
    }

    if (this.options.destroyOnClose) {
      this.destroy();
    }
  };

  AdderDialog.prototype.onSearchKeyUp = function (text) {
    var dialog = this;

    if (this.searchRequest !== null) {
      this.searchRequest.abort();
    }

    this.searchRequest = $.getJSON(this.options.searchOptions.searchSource, {
      term: text,
      code: this.options.searchOptions.code,
      ajax: 'ajax'
    }, function (results) {
      dialog.searchRequest = null;
      var no_data = !$(results).length;

      dialog.searchResultList.empty();
      dialog.searchResultList.toggle(!no_data);
      dialog.noSearchResultsWrapper.toggle(no_data);

      $(results).each(function (index, result) {
        var item = $("<li />", {'data-label': result['value'], 'data-id': result['id']})
          .append($('<span />', {class: 'auto-width'}).text(result['value']));
        dialog.searchResultList.append(item);
      });
    });
  };

  exports.AdderDialog = AdderDialog;

}(OpenEyes.UI, OpenEyes.Util, OpenEyes.Util.EventEmitter));