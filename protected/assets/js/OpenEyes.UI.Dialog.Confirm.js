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
(function(exports, Util) {

	'use strict';

	// Base Dialog.
	var Dialog = exports;

	/**
	 * ConfirmDialog constructor. The ConfirmDialog extends the base Dialog and provides
	 * an 'Ok' and 'Cancel' button for the user to click on.
	 * @constructor
	 * @name OpenEyes.UI.Dialog.Confirm
	 * @tutorial dialog_confirm
	 * @extends OpenEyes.UI.Dialog
	 * @example
	 * var alert = new OpenEyes.UI.Dialog.Confirm({
	 *   content: 'Here is some content.'
	 * });
	 * alert.open();
	 */
	function ConfirmDialog(options) {

		options = $.extend(true, {}, ConfirmDialog._defaultOptions, options);
		options.content = !options.url ? options.content : '';

		Dialog.call(this, options);
	}

	Util.inherits(Dialog, ConfirmDialog);

	/**
	 * The default confirm dialog options. These options will be merged into the
	 * default dialog options.
	 * @name OpenEyes.UI.Dialog.Confirm#_defaultOptions
	 * @private
	 */
	ConfirmDialog._defaultOptions = {
		modal: true,
		minHeight: 'auto',
		title: 'Confirm',
		dialogClass: 'dialog confirm',
		okButton: 'OK',
		cancelButton: 'Cancel',
		okButtonClassList: 'secondary small confirm ok',
		cancelButtonClassList: 'warning small confirm cancel',
		templateSelector: '#dialog-confirm-template'
	};

	/**
	 * Get the dialog content. Do some basic content formatting, then compile
	 * and return the alert dialog template.
	 * @name OpenEyes.UI.Dialog.Confirm#getContent
	 * @method
	 * @private
	 * @param {string} content - The main alert dialog content to display.
	 * @returns {string}
	 */
	ConfirmDialog.prototype.getContent = function(options) {
		// Compile the template, get the HTML
		return this.compileTemplate({
			selector: options.templateSelector,
			data: {
				content: options.content,
                leftPanelContent: options.leftPanelContent,
                rightPanelContent: options.rightPanelContent,
				okButton: options.okButton,
				cancelButton: options.cancelButton,
				okButtonClassList: options.okButtonClassList,
				cancelButtonClassList: options.cancelButtonClassList
			}
		});
	};

	/**
	 * Bind events
	 * @name OpenEyes.UI.Dialog.Confirm#bindEvents
	 * @method
	 * @private
	 */
	ConfirmDialog.prototype.bindEvents = function() {
		Dialog.prototype.bindEvents.apply(this, arguments);
		this.content.on('click', '.ok', this.onOKButtonClick.bind(this));
		this.content.on('click', '.cancel', this.onCancelButtonClick.bind(this));
	};

	/** Event handlers */

	ConfirmDialog.prototype.onDialogClose = function(e) {

		Dialog.prototype.onDialogClose.apply(this, arguments);

		// If user pressed escape key.
		if (e && e.keyCode && e.keyCode === 27) {
			this.emit('cancel');
		}

		// If user clicked on close button.
		if ($(e.srcElement).hasClass('ui-dialog-titlebar-close')) {
			this.emit('cancel');
		}
	}

	/**
	 * 'OK' button click handler. Simply close the dialog on click.
	 * @name OpenEyes.UI.Dialog.Confirm#onButtonClick
	 * @fires OpenEyes.UI.Dialog.Confirm#ok
	 * @method
	 * @private
	 */
	ConfirmDialog.prototype.onOKButtonClick = function() {

		this.close();

		/**
		 * Emitted after the use has clicked on the 'OK' button.
		 *
		 * @event OpenEyes.UI.Dialog.Confirm#ok
		 */
		this.emit('ok');
	};

	/**
	 * 'Cancel' button click handler. Simply closes the dialog on click.
	 * @name OpenEyes.UI.Dialog.Confirm#onButtonClick
	 * @fires OpenEyes.UI.Dialog.Confirm#cancel
	 * @method
	 * @private
	 */
	ConfirmDialog.prototype.onCancelButtonClick = function() {

		this.close();

		/**
		 * Emitted after the use has clicked on the 'Cancel' button.
		 *
		 * @event OpenEyes.UI.Dialog.Confirm#cancel
		 */
		this.emit('cancel');
	};

	exports.Confirm = ConfirmDialog;

}(OpenEyes.UI.Dialog, OpenEyes.Util));