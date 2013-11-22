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
(function(exports) {

	var NAMESPACE = 'sticky';
	var win = $(window);

	function StickyElement(element, options) {

		this.element = $(element);
		if (!this.element.length) return;

		this.options = $.extend({}, StickyElement.defaultOptions, options);
		this.elementOffset = this.element.offset();
		this.wrapperHeight = this.options.wrapperHeight.call(this);

		this.wrapElement()
		this.bindEvents();
	}

	StickyElement.defaultOptions = {
		wrapperClass: 'sticky-wrapper',
		stuckClass: 'stuck',
		offset: 0,
		debug: false,
		wrapperHeight: function() {
			return this.element.height();
		},
		enableHandler: function() {
			this.enable();
		},
		disableHandler: function() {
			this.disable();
		}
	};

	StickyElement.prototype.wrapElement = function() {
		this.element.wrap($('<div />', {
			'class': this.options.wrapperClass
		}));
		this.wrapper = this.element.parent();
	};

	StickyElement.prototype.bindEvents = function() {
		win.on('scroll.' + NAMESPACE, this.onWindowScroll.bind(this));
	};

	StickyElement.prototype.enable = function() {
		this.wrapper.height(this.wrapperHeight);
		this.element.addClass(this.options.stuckClass);
	};

	StickyElement.prototype.disable = function() {
		this.wrapper.height('auto');
		this.element.removeClass(this.options.stuckClass);
	};

	StickyElement.prototype.onWindowScroll = function(e) {

		var offset = $.isFunction(this.options.offset) ? this.options.offset() : this.options.offset;
		var winTop = win.scrollTop();
		var elementTop = this.elementOffset.top + offset;

		if (winTop >= elementTop) {
			this.options.enableHandler.call(this);
		} else {
			this.options.disableHandler.call(this);
		}
	};

	exports.StickyElement = StickyElement;

	exports.StickyElements = {
		refresh: function() {
			win.trigger('scroll.' + NAMESPACE);
		}
	};

}(this.OpenEyes.UI));