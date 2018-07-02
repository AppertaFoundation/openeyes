(function (exports, Util, EventEmitter) {

  'use strict';

  function AdderDialog(options) {

    EventEmitter.call(this);
    this.options = $.extend(true, {}, AdderDialog._defaultOptions, options);
    this.create();
    this.bindEvents();
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
    items: [],
    openButton: null,
    onOpen: null,
    onClose: null,
    onSelect: null,
    onReturn: null,
    onAdd: null,
    multiSelect: false,
    id: null,
    popupClass: 'oe-add-select-search auto-width',
    width: 440,
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

    this.content = $('<div />', {class: this.options.popupClass, id: this.options.id});
    var $closeButton = $('<div />', {class: 'close-icon-btn'}).append($('<i />', {class: 'oe-i remove-circle medium'}));
    this.content.append($closeButton);
    $closeButton.click(function () {
      AdderDialog.this.close();
    });

    var $addButton = $('<button />', {
      class: 'button hint green add-icon-btn',
      type: 'button'
    }).append($('<i />', {class: 'oe-i plus pro-theme'}));
    this.content.append($closeButton);
    this.setClose($(this.content).find('.close-icon-btn'));

    this.setOpen(this.options.openButton);

    this.content.insertAfter(this.options.openButton);
    this.popup = this.options.openButton.siblings('.oe-add-select-search');
    this.generateContent();

    this.popup.find('.close-icon-btn').click(function () {
      dialog.close();
    });

    if (this.options.onSelect) {
      this.popup.on('clicl', 'li', this.options.onSelect);
    } else {
      this.popup.on('click', 'li', function () {
        if (!$(this).hasClass('selected')) {
          if (!dialog.options.multiSelect) {
            $(this).parent('ul').find('li').removeClass('selected');
          }
          $(this).addClass('selected');
        } else {
          $(this).removeClass('selected');
        }
      });
    }

    this.popup.on('click', '.add-icon-btn', function() {
      if(dialog.options.onReturn) {
        var selectedItems = dialog.getSelectedItems();
        var result = dialog.options.onReturn(dialog, selectedItems);
        if (result) {
          dialog.close();
        }
      } else {
        dialog.close();
      }
    });
  };

  AdderDialog.prototype.generateContent = function () {
    if (this.options.items) {
      this.generateItemList();
    }
  };

  AdderDialog.prototype.getSelectedItems = function() {
    return this.popup.find('li.selected').map(function() {
      return {'id': $(this).data('id'), 'label': $(this).data('label') };
    }).get();
  };

  AdderDialog.prototype.generateItemList = function () {
    var $container = $('<div />', {class: 'flex-layout flex-top flex-left'}).appendTo(this.popup);
    var $list = $('<ul />', {class: 'add-options cols-full'}).appendTo($container);

    this.options.items.forEach(function (item) {
      var $listItem = $('<li />', {'data-label': item['label'], 'data-id': item['id']});
      $('<span />', {class: 'auto-width'}).text(item['label']).appendTo($listItem);
      $listItem.appendTo($list);
    });
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
   * Binds common dialog event handlers.
   * @name OpenEyes.UI.Dialog#create
   * @method
   * @private
   */
  AdderDialog.prototype.bindEvents = function () {
    this.content.on({
      dialogclose: this.onDialogClose.bind(this),
      dialogopen: this.onDialogOpen.bind(this)
    });
  };


  /**
   * Sets the dialog title.
   * @name OpenEyes.UI.Dialog#setTitle
   * @method
   * @public
   */
  AdderDialog.prototype.setTitle = function (title) {
    $(this.content).find('.title').val(title);
  };

  /**
   * Calculates and sets the dialog dimensions.
   * @name OpenEyes.UI.Dialog#setDimensions
   * @method
   * @private
   */
  AdderDialog.prototype.setDimensions = function () {
    var dimensions = this.getDimensions();
    this.instance.option('width', dimensions.width);
    this.instance.option('height', dimensions.height);
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

  /**
   * Destroys the dialog. Removes all elements from the DOM and detaches all
   * event handlers.
   * @name OpenEyes.UI.Dialog#destroy
   * @fires OpenEyes.UI.Dialog#destroy
   * @method
   * @public
   *
   */
  AdderDialog.prototype.destroy = function () {
    this.content.remove();

    /**
     * Emitted after the dialog has been destroyed and completed removed from the DOM.
     *
     * @event OpenEyes.UI.Dialog#destroy
     */
    this.emit('destroy');
  };

  AdderDialog.prototype.setClose = function (closeButton) {
    var dialog = this;
    closeButton.click(function () {
      dialog.close();
    });
  };

  AdderDialog.prototype.setOpen = function (openButton) {
    var dialog = this;
    openButton.click(function () {
      dialog.open();
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

  exports.AdderDialog = AdderDialog;

}(OpenEyes.UI, OpenEyes.Util, OpenEyes.Util.EventEmitter));