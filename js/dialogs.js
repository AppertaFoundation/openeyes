/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

var OpenEyes = window.OpenEyes || {};
OpenEyes.Dialog = OpenEyes.Dialog || {};

(function() {

  // Set the jQuery UI Dialog default options.
  $.extend($.ui.dialog.prototype.options, {
    dialogClass: 'dialog',
    show: 'fade'
  });

  var EventEmitter = OpenEyes.Util.EventEmitter;

  /**
   * Dialog constructor.
   * @name Dialog
   * @constructor
   * @example
   * var dialog = new OpenEyes.Dialog({
   *   title: 'Title here',
   *   content: 'Here is some content.'
   * });
   * dialog.on('open', function() {
   *   console.log('The dialog is now open');
   * });
   * dialog.on('close', function() {
   *   console.log('The dialog is now closed.');
   * });
   * dialog.on('destroy', function() {
   *   console.log('The dialog has been destroyed.');
   * });
   * dialog.open();
   */
  function Dialog(options) {

    EventEmitter.call(this);

    this.options = $.extend(true, {}, Dialog.defaultOptions, options);
    this.create();
    this.bindEvents();

    if (this.options.url) {
      this.loadContent();
    }
  }

  Dialog.inherits(EventEmitter);

  /**
   * The default dialog options. Custom options will be merged with these.
   * @name Dialog#defaultOptions
   * @property
   */
  Dialog.defaultOptions = {
    content: '',
    destroyOnClose: true,
    url: null,
    autoOpen: false,
    title: '',
    modal: true,
    dialogClass: 'dialog',
    resizable: false,
    draggable: false,
    width: 400,
    height: 'auto',
    minHeight: 'auto',
    show: 'fade'
  };

  /**
   * Creates and stores the dialog container, and creates a new jQuery UI
   * instance on the container.
   * @name Dialog#create
   * @method
   * @private
   */
  Dialog.prototype.create = function() {
    this.content = $('<div>' + (this.options.content || '') + '</div>');
    this.content.dialog(this.options);
    this.instance = this.content.data('ui-dialog');
  };

  /**
   * Binds common dialog event handlers.
   * @name Dialog#create
   * @method
   * @private
   */
  Dialog.prototype.bindEvents = function() {
    this.content.on({
      dialogclose: this.onDialogClose.bind(this),
      dialogopen: this.onDialogOpen.bind(this)
    });
  };

  /**
   * Gets a script template from the DOM, compiles it using Mustache, and
   * returns the HTML.
   * @name Dialog#compileTemplate
   * @method
   * @private
   * @param {object} options - An options object container the template selector and data.
   * @returns {string}
   */
  Dialog.prototype.compileTemplate = function(options) {

    var template = $(options.selector).html();

    if (!template) {
      throw new Error('Unable to compile dialog template. Template not found: ' + options.selector);
    }

    return Mustache.render(template, options.data || {});
  };

  /**
   * Sets a 'loading' message and retrieves the dialog content via AJAX.
   * @name Dialog#loadContent
   * @method
   * @private
   */
  Dialog.prototype.loadContent = function() {
    this.content.html('Loading...');
    this.content.load(this.options.url, this.onContentLoaded.bind(this));
  };

  /**
   * When loading content, if the request fails, then show an error message.
   * @name Dialog#showContentLoadError
   * @method
   * @private
   */
  Dialog.prototype.showContentLoadError = function() {
    this.content.html('Sorry, there was an error retrieving the content. Please try again.');
  };

  /**
   * Repositions the dialog in the center of the page.
   * @name Dialog#reposition
   * @method
   */
  Dialog.prototype.reposition = function() {
    this.instance._position(this.instance._position());
  };

  /**
   * Opens (shows) the dialog.
   * @name Dialog#open
   * @method
   */
  Dialog.prototype.open = function() {
    this.instance.open();
  };

  /**
   * Closes (hides) the dialog, and optionally destroys it.
   * @name Dialog#close
   * @method
   */
  Dialog.prototype.close = function() {

    this.instance.close();

    if (this.options.destroyOnClose) {
      this.destroy();
    }
  };

  /**
   * Destroys the dialog. Removes all elements from the DOM and detaches all
   * event handlers.
   * @name Dialog#destroy
   * @method
   */
  Dialog.prototype.destroy = function() {
    this.instance.destroy();
    this.content.remove();
    this.emit('destroy');
  };

  /** Event handlers */

  /**
   * Emit the 'open' event after the dialog has opened.
   * @name Dialog#onDialogOpen
   * @method
   * @private
   */
  Dialog.prototype.onDialogOpen = function() {
    this.emit('open');
  };

  /**
   * Emit the 'close' event after the dialog has closed.
   * @name Dialog#onDialogClose
   * @method
   * @private
   */
  Dialog.prototype.onDialogClose = function() {
    this.emit('close');
  };

  /**
   * Reposition the dialog after the content has been loaded.
   * @name Dialog#onContentLoaded
   * @method
   * @private
   */
  Dialog.prototype.onContentLoaded = function(response, status, xhr) {
    if (status === 'error') {
      this.showContentLoadError();
    }
    this.reposition();
  };

  OpenEyes.Dialog = Dialog;

}());

(function() {

  var Dialog = OpenEyes.Dialog;

  /**
   * AlertDialog constructor. The AlertDialog extends the base Dialog and provides
   * an 'Ok' button for the user to click on.
   * @name AlertDialog
   * @constructor
   * @extends Dialog
   * @example
   * var alert = new OpenEyes.Dialog.Alert({
   *   content: 'Here is some content.'
   * });
   * alert.open();
   */
  function AlertDialog(options) {

    options = $.extend(true, options, AlertDialog.defaultOptions);
    options.content = this.getContent(options.content);

    Dialog.call(this, options);
  }

  AlertDialog.inherits(Dialog);

  /**
   * The default alert dialog options. These options will be merged into the
   * default dialog options.
   * @name AlertDialog#defaultOptions
   * @property
   */
  AlertDialog.defaultOptions = {
    modal: true,
    width: 400,
    minHeight: 'auto',
    title: 'Alert',
    dialogClass: 'dialog alert'
  };

  /**
   * Get the dialog content. Do some basic content formatting, then compile
   * and return the alert dialog template.
   * @name AlertDialog#getContent
   * @method
   * @private
   * @param {string} content - The main alert dialog content to display.
   * @returns {string}
   */
  AlertDialog.prototype.getContent = function(content) {

    // Replace new line characters with html breaks
    content = (content || '').replace(/\n/g, '<br/>');

    // Compile the template, get the HTML
    return this.compileTemplate({
      selector: '#dialog-alert-template',
      data: {
        content: content
      }
    });
  };

  /**
   * Bind events
   * @name AlertDialog#bindEvents
   * @method
   * @private
   */
  AlertDialog.prototype.bindEvents = function() {
    Dialog.prototype.bindEvents.apply(this, arguments);
    this.content.on('click', '.ok', this.onButtonClick.bind(this));
  };

  /** Event handlers */

  /**
   * 'OK' button click handler. Simply close the dialog on click.
   * @name AlertDialog#onButtonClick
   * @method
   * @private
   */
  AlertDialog.prototype.onButtonClick = function() {
    this.close();
  };

  OpenEyes.Dialog.Alert = AlertDialog;
}());