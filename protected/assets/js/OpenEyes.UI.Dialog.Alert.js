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
(function(exports, Util) {

	'use strict';

	// Base Dialog.
	var Dialog = exports;

	/**
	 * AlertDialog constructor. The AlertDialog extends the base Dialog and provides
	 * an 'Ok' button for the user to click on.
	 * @constructor
	 * @name OpenEyes.UI.Dialog.Alert
	 * @tutorial dialog_alert
	 * @extends OpenEyes.UI.Dialog
	 * @example
	 * var alert = new OpenEyes.UI.Dialog.Alert({
	 *   content: 'Here is some content.'
	 * });
	 * alert.open();
	 */
	function AlertDialog(options) {

		options = $.extend(true, {}, AlertDialog._defaultOptions, options);
		options.content = this.getContent(options);

		Dialog.call(this, options);
	}

	Util.inherits(Dialog, AlertDialog);

	/**
	 * The default alert dialog options. These options will be merged into the
	 * default dialog options.
	 * @name OpenEyes.UI.Dialog.Alert#_defaultOptions
	 * @private
	 */
	AlertDialog._defaultOptions = {
		modal: true,
		width: 400,
		minHeight: 'auto',
		title: 'Alert',
		dialogClass: 'dialog alert'
	};

	/**
	 * Get the dialog content. Do some basic content formatting, then compile
	 * and return the alert dialog template.
	 * @name OpenEyes.UI.Dialog.Alert#getContent
	 * @method
	 * @private
	 * @param {string} content - The main alert dialog content to display.
	 * @returns {string}
	 */
	AlertDialog.prototype.getContent = function(options) {

		// Replace new line characters with html breaks
		options.content = (options.content || '').replace(/\n/g, '<br/>');

		// Compile the template, get the HTML
		return this.compileTemplate({
			selector: '#dialog-alert-template',
			data: {
				content: options.content
			}
		});
	};

	/**
	 * Bind events
	 * @name OpenEyes.UI.Dialog.Alert#bindEvents
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
	 * @name OpenEyes.UI.Dialog.Alert#onButtonClick
	 * @method
	 * @private
	 */
	AlertDialog.prototype.onButtonClick = function() {
		this.close();
	};

	exports.Alert = AlertDialog;

}(OpenEyes.UI.Dialog, OpenEyes.Util));