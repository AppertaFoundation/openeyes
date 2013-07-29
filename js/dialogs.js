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

  var EventEmitter = OpenEyes.Util.EventEmitter;

  function Dialog(options) {
    EventEmitter.call(this);
    this.options = $.extend(true, {}, Dialog.defaultOptions, options);
    this.create();
    this.bindEvents();
  }

  Dialog.inherits(EventEmitter);

  Dialog.defaultOptions = {
    content: '',
    destroyOnClose: true,
    ui: {
      autoOpen: false,
      title: '',
      modal: false,
      dialogClass: 'dialog',
      resizable: false,
      draggable: false,
      show: 'fade'
    }
  };

  Dialog.prototype.create = function() {
    this.content = $('<div>' + (this.options.content || '') + '</div>');
    this.content.dialog(this.options.ui);
    this.instance = this.content.data('ui-dialog');
  };

  Dialog.prototype.bindEvents = $.noop;

  Dialog.prototype.compileTemplate = function(options) {
    var template = $(options.selector).html();
    var html = Mustache.render(template, options.data || {});
    return html;
  };

  Dialog.prototype.open = function() {
    this.instance.open();
    this.emit('open');
  };

  Dialog.prototype.close = function() {

    this.instance.close();
    this.emit('close');

    if (this.options.destroyOnClose) {
      this.destroy();
    }
  };

  Dialog.prototype.destroy = function() {
    this.instance.destroy();
    this.content.remove();
    this.emit('destroy');
  };

  OpenEyes.Dialog = Dialog;

}());

(function() {

  var Dialog = OpenEyes.Dialog;

  function AlertDialog(options) {

    options = $.extend(true, options, AlertDialog.defaultOptions);
    options.content = this.getTemplate(options.content);

    Dialog.call(this, options);
  }

  AlertDialog.inherits(Dialog);

  AlertDialog.defaultOptions = {
    ui: {
      modal: true,
      width: 400,
      minHeight: 'auto',
      title: 'Alert',
      dialogClass: 'dialog alert'
    }
  };

  AlertDialog.prototype.getTemplate = function(content) {

    // Replace new line characters with html breaks
    content = content.replace(/\n/g, '<br/>');

    // Compile the template, get the HTML
    content = this.compileTemplate({
      selector: '#dialog-alert-template',
      data: {
        content: content
      }
    });

    return content;
  };

  AlertDialog.prototype.bindEvents = function() {
    this.content.on('click', '.ok', this.onButtonClick.bind(this));
  };

  AlertDialog.prototype.onButtonClick = function() {
    this.close();
  };

  OpenEyes.Dialog.Alert = AlertDialog;
}());