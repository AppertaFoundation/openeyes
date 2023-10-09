/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

(function (exports, Util, EventEmitter) {

    'use strict';

    /**
     * Dialog constructor.
     * @constructor
     * @name OpenEyes.UI.Dialog
     * @tutorial dialog
     * @memberOf OpenEyes.UI
     * @extends OpenEyes.Util.EventEmitter
     * @example
     * var dialog = new OpenEyes.UI.Dialog({
	 *	title: 'Title here',
	 *	content: 'Here is some content.'
	 * });
     * dialog.on('open', function() {
	 *	console.log('The dialog is now open');
	 * });
     * dialog.open();
     */
    function Dialog(options) {

        EventEmitter.call(this);

        this.options = $.extend(true, {}, Dialog._defaultOptions, options);

        this.create();
        this.bindEvents();

        // Load dialog content in an iframe.
        if (this.options.iframe) {
            this.loadIframeContent();
        }
        // Load dialog content via an AJAX request.
        else if (this.options.url) {
            this.loadContent();
        }
    }

    Util.inherits(EventEmitter, Dialog);

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
    Dialog._defaultOptions = {
        content: null,
        destroyOnClose: true,
        url: null,
        data: null,
        id: null,
        iframe: null,
        autoOpen: false,
        title: null,
        popupClass: 'oe-popup',
        popupContentClass: 'oe-popup-content',
        modal: true,
        dialogClass: 'dialog',
        resizable: false,
        draggable: false,
        constrainToViewport: false,
        height: 'auto',
        minHeight: 'auto',
        show: 'fade'
    };

    /**
     * Creates and stores the dialog container, and creates a new jQuery UI
     * instance on the container.
     * @name OpenEyes.UI.Dialog#create
     * @method
     * @private
     */
    Dialog.prototype.create = function () {

        // Create the dialog content div.
        this.content = $('<div />', {class: 'oe-popup-wrap'});
        var closeButton = '<div class="close-icon-btn"><i class="oe-i remove-circle pro-theme"></i></div>';
        var popup = $('<div class="' + this.options.popupClass + '"></div>');
        if (this.options.width) {
            popup.css('width', this.options.width);
        }
        
        $('<div class="title">' + this.options.title + '</div>' + closeButton + '<div class="' + this.options.popupContentClass + '"></div>').appendTo(popup);

        this.content.append(popup);
        // Add default content (if any exists)
        if (!this.options.url) {
            this.setContent(this.options.content);
        }
    };

    /**
     * Add content to the dialog.
     * @name OpenEyes.UI.Dialog#setContent
     * @method
     * @public
     */
    Dialog.prototype.setContent = function (content) {
        if (typeof(this.getContent) == 'function') {
            var options = $.extend({}, this.options, {content: content});
            content = this.getContent(options);
        }
        $(this.content).find('.oe-popup-content').append(content);
        if ($(':input[type="submit"]', this.content).length) {
            $(':input[type="submit"]', this.content).get(0).focus();
        }
    };

    Dialog.prototype.removeContent = function () {
        document.querySelector('.oe-popup-content').innerHTML = '';
    };

    /**
     * Binds common dialog event handlers.
     * @name OpenEyes.UI.Dialog#create
     * @method
     * @private
     */
    Dialog.prototype.bindEvents = function () {
        this.content.on({
            dialogclose: this.onDialogClose.bind(this),
            dialogopen: this.onDialogOpen.bind(this)
        });
    };

    /**
     * Gets a script template from the DOM, compiles it using Mustache, and
     * returns the HTML.
     * @name OpenEyes.UI.Dialog#compileTemplate
     * @method
     * @private
     * @param {object} options - An options object container the template selector and data.
     * @returns {string}
     */
    Dialog.prototype.compileTemplate = function (options) {

        var template = $(options.selector).html();

        if (!template) {
            throw new Error('Unable to compile dialog template. Template not found: ' + options.selector);
        }

        return Mustache.render(template, options.data || {}, options.partials || {});
    };

    /**
     * Sets the dialog to be in a loading state.
     * @name OpenEyes.UI.Dialog#setLoadingState
     * @method
     * @private
     */
    Dialog.prototype.setLoadingState = function () {
        this.content.addClass('loading');
        this.setTitle('Loading...');
    };

    /**
     * Removes the loading state from the dialog.
     * @name OpenEyes.UI.Dialog#removeLoadingState
     * @method
     * @private
     */
    Dialog.prototype.removeLoadingState = function () {
        this.content.removeClass('loading');
    };

    /**
     * Sets a 'loading' message and retrieves the dialog content via AJAX.
     * @name OpenEyes.UI.Dialog#loadContent
     * @method
     * @private
     */
    Dialog.prototype.loadContent = function () {

        this.setLoadingState();

        this.xhr = $.ajax({
            url: this.options.url,
            data: this.options.data
        });

        this.xhr.done(this.onContentLoadSuccess.bind(this));
        this.xhr.fail(this.onContentLoadFail.bind(this));
        this.xhr.always(this.onContentLoad.bind(this));
    };

    /**
     * Sets a 'loading' message and creates an iframe with the appropriate src attribute.
     * @name OpenEyes.UI.Dialog#loadIframeContent
     * @method
     * @private
     */
    Dialog.prototype.loadIframeContent = function () {

        this.setLoadingState();

        this.iframe = $('<iframe />', {
            width: '100%',
            height: '99%',
            frameborder: 0
        }).hide();

        // We're intentionally setting the load handler before setting the src.
        this.iframe.on('load', this.onIframeLoad.bind(this));
        this.iframe.attr({
            src: this.options.iframe
        });

        // Add the iframe to the DOM.
        this.setContent(this.iframe);
    };

    /**
     * Sets the dialog title.
     * @name OpenEyes.UI.Dialog#setTitle
     * @method
     * @public
     */
    Dialog.prototype.setTitle = function (title) {
        $(this.content).find('.title').text(title);
    };

    /**
   * Calculates the dialog dimensions. If OpenEyes.UI.Dialog#options.constrainToViewport is
   * set, then the dimensions will be calculated so that the dialog will not be
   * displayed outside of the browser viewport.
   * @name OpenEyes.UI.Dialog#getDimensions
   * @method
   * @private
   */
  Dialog.prototype.getDimensions = function () {

    var dimensions = {
      width: this.options.width,
      height: this.options.height
    };

    // We're just ensuring the maximum height of the dialog does not exceed either
    // the specified height (set in the options), or the height of the viewport. We're
    // not 'fitting' to the viewport.
    if (this.options.constrainToViewport) {
      var actualDimensions = this.getActualDimensions();
      var offset = 40;
      dimensions.width = Math.min(actualDimensions.width, $(window).width() - offset);
      dimensions.height = Math.min(actualDimensions.height, $(window).height() - offset);
    }

    return dimensions;
  };

  /**
   * Gets the actual dimensions of the dialog. We need to ensure the dialog
   * is open to calculate the dimensions.
   * @return {object} An object containing the width and height dimensions.
   */
  Dialog.prototype.getActualDimensions = function () {

    var isOpen = this.instance.isOpen();
    var destroyOnClose = this.options.destroyOnClose;

    if (!isOpen) {
      this.options.destroyOnClose = false;
      this.instance.open();
    }

    var dimensions = {
      width: parseInt(this.options.width, 10) || this.instance.uiDialog.outerWidth(),
      height: parseInt(this.options.height, 10) || this.instance.uiDialog.outerHeight()
    };

    if (!isOpen) {
      this.instance.close();
      this.options.destroyOnClose = destroyOnClose;
    }

    return dimensions;
  };

  /**
   * Calculates and sets the dialog dimensions.
   * @name OpenEyes.UI.Dialog#setDimensions
   * @method
   * @private
   */
  Dialog.prototype.setDimensions = function () {
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
  Dialog.prototype.open = function () {
    $('body').prepend(this.content);
    this.setClose($(this.content).find('.close-icon-btn'));
    this.emit("open");
  };

    /**
     * Opens (shows) the dialog.
     * @name OpenEyes.UI.Dialog#open
     * @method
     * @public
     */
    Dialog.prototype.openOnTop = function () {
        $('body').append(this.content);
        this.emit("open");
    };

  /**
   * Closes (hides) the dialog.
   * @name OpenEyes.UI.Dialog#close
   * @method
   * @public
   */
  Dialog.prototype.close = function () {
    this.emit("close");
    $('.oe-popup-wrap').not('#js-overlay').remove();
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
    Dialog.prototype.destroy = function () {

        if (this.xhr) {
            this.xhr.abort();
        }
        if (this.iframe) {
            this.iframe.remove();
        }

        this.content.remove();

        /**
         * Emitted after the dialog has been destroyed and completed removed from the DOM.
         *
         * @event OpenEyes.UI.Dialog#destroy
         */
        this.emit('destroy');
    };

    Dialog.prototype.setClose = function (closeButton) {
        let dialog = this;
        closeButton.click(function () {
            if (typeof dialog.options.closeCallback === 'function') {
                dialog.options.closeCallback();
            }
            dialog.emit("close");
            $('.oe-popup-wrap').not('#js-overlay').remove();
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
    Dialog.prototype.onDialogOpen = function () {
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
    Dialog.prototype.onDialogClose = function () {
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

    /**
     * Content load handler. This method is always executed *after* the content
     * request completes (whether there was an error or not), and is executed after
     * any success or fail handlers. This method removes the loading state of the
     * dialog, and repositions it in the center of the screen.
     * @name OpenEyes.UI.Dialog#onContentLoad
     * @method
     * @private
     */
    Dialog.prototype.onContentLoad = function () {
        this.removeLoadingState();
    };

    /**
     * Content load success handler. Sets the dialog content to be the response of
     * the content request.
     * @name OpenEyes.UI.Dialog#onContentLoadSuccess
     * @method
     * @private
     */
    Dialog.prototype.onContentLoadSuccess = function (response) {
        this.setTitle(this.options.title);
        this.setContent(response);
    };

    /**
     * Content load fail handler. This method is executed if the content request
     * fails, and shows an error message.
     * @name OpenEyes.UI.Dialog#onContentLoadFail
     * @method
     * @private
     */
    Dialog.prototype.onContentLoadFail = function () {
        this.setTitle('Error');
        this.setContent('Sorry, there was an error retrieving the content. Please try again.');
    };

    /**
     * iFrame load handler. This method is always executed after the iFrame
     * source is loaded. This method removes the loading state of the
     * dialog, and repositions it in the center of the screen.
     * @name OpenEyes.UI.Dialog#onIframeLoad
     * @method
     * @private
     */
    Dialog.prototype.onIframeLoad = function () {
        this.setTitle(this.options.title);
        this.iframe.show();
        this.onContentLoad();
    };

    exports.Dialog = Dialog;

}(OpenEyes.UI, OpenEyes.Util, OpenEyes.Util.EventEmitter));