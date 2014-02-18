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

	/**
	 * StickyElement constructor.
	 * @name OpenEyes.UI.StickyElement
	 * @constructor
	 * @param {Mixed}  element Element can be a selector string, a DOM element or a
	 * jQuery instance.
	 * @param {Object} options The custom options for this instance.
	 */
	function StickyElement(element, options) {

		this.element = $(element);
		if (!this.element.length) return;

		this.options = $.extend({}, StickyElement._defaultOptions, options);
		this.elementOffset = this.element.offset();
		this.wrapperHeight = this.options.wrapperHeight(this);

		this.wrapElement();
		this.bindEvents();
	}

	/**
	 * StickyElement default options.
	 * @name OpenEyes.UI.StickyElement#_defaultOptions
	 * @type {Object}
	 */
	StickyElement._defaultOptions = {
		wrapperClass: 'sticky-wrapper',
		stuckClass: 'stuck',
		offset: 0,
		debug: false,
		wrapperHeight: function(instance) {
			return instance.element.height();
		},
		enableHandler: function(instance) {
			instance.enable();
		},
		disableHandler: function(instance) {
			instance.disable();
		}
	};

	/**
	 * Wraps the element in a container div.
	 * @name OpenEyes.UI.StickyElement#wrapElement
	 * @private
	 * @method
	 */
	StickyElement.prototype.wrapElement = function() {
		this.element.wrap($('<div />', {
			'class': this.options.wrapperClass
		}));
		this.wrapper = this.element.parent();
	};

	/**
	 * Binds DOM events to method handlers.
	 * @name OpenEyes.UI.StickyElement#bindEvents
	 * @private
	 * @method
	 */
	StickyElement.prototype.bindEvents = function() {
		win.on('scroll.' + NAMESPACE, this.onWindowScroll.bind(this));
	};

	/**
	 * Make the element sticky.
	 * @name OpenEyes.UI.StickyElement#enable
	 * @method
	 */
	StickyElement.prototype.enable = function() {
		this.wrapper.height(this.wrapperHeight);
		this.element.addClass(this.options.stuckClass);
	};

	/**
	 * Unstick the element.
	 * @name OpenEyes.UI.StickyElement#disable
	 * @method
	 */
	StickyElement.prototype.disable = function() {
		this.wrapper.height('auto');
		this.element.removeClass(this.options.stuckClass);
	};

	/**
	 * Window scroll handler. This method compares the offset of the element to the
	 * window scroll position and determines if the element should be sticky or not.
	 * @name OpenEyes.UI.StickyElement#onWindowScroll
	 * @method
	 * @private
	 */
	StickyElement.prototype.onWindowScroll = function() {

		var offset = $.isFunction(this.options.offset) ? this.options.offset() : this.options.offset;
		var winTop = win.scrollTop();
		var elementTop = this.elementOffset.top + offset;

		// [OE-4014] This accounts for "over-scroll" that occurs when using a trackpad/touch
		// device. Offsets are calculated relative to the document, and as we're using
		// window.scrollTop in our calculations, we need to ensure the scroll position
		// value never exceeds the height of the document.
		var scrollHeight = $(document).height() - win.height();
		if (winTop > scrollHeight) {
			winTop -= (winTop - scrollHeight);
		}

		if (winTop >= elementTop) {
			this.options.enableHandler(this);
		} else {
			this.options.disableHandler(this);
		}
	};

	exports.StickyElement = StickyElement;

	exports.StickyElements = {
		refresh: function() {
			win.trigger('scroll.' + NAMESPACE);
		}
	};

}(this.OpenEyes.UI));