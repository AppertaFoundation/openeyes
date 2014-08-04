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

(function(exports, Util, EventEmitter) {

	'use strict';

	function Tooltip(options) {
		EventEmitter.call(this);
		this.options = $.extend(true, {}, Tooltip._defaultOptions, options);
		this.create();
	}

	Tooltip.prototype = Object.create(EventEmitter.prototype);

	Tooltip._defaultOptions = {
		className: 'quicklook tooltip',
		content: '',
		offset: {
			x: 0,
			y: 0
		},
		viewPortOffset: {
			x: 0,
			y: 0
		}
	};

	Tooltip.prototype.create = function() {

		this.container = $('<div />', {
			'class': this.options.className
		}).appendTo(document.body);

		this.setContent(this.options.content);
	};

	Tooltip.prototype.setContent = function(content) {
		this.container.html(content);
	};

	Tooltip.prototype.show = function(x, y) {
		this.container.css(this.getPosition(x, y));
	};

	Tooltip.prototype.getPosition = function(x, y) {

		this.container.show();

		var opts = this.options;

		var viewPortX = x - $(window).scrollLeft();
		var viewPortY = y - $(window).scrollTop();

		var viewPortWidth = $(window).width();
		var viewPortHeight = $(window).height();

		var width = this.container.outerWidth();
		var height = this.container.outerHeight();

		// Off-screen to the right?
		if (width + viewPortX + opts.offset.x + opts.viewPortOffset.x >= viewPortWidth) {
			x -= (width + opts.offset.x);
		} else {
			x += opts.offset.x;
		}

		// Off-screen to the bottom?
		if (height + viewPortY + opts.offset.y + opts.viewPortOffset.y >= viewPortHeight) {
			y -= (height + opts.offset.y);
		} else {
			y += opts.offset.y;
		}

		return { left: x, top: y };
	};

	Tooltip.prototype.hide = function() {
		this.container.hide();
	};

	Tooltip.prototype.destroy = function() {
		this.container.empty().remove();
	};

	exports.Tooltip = Tooltip;

}(OpenEyes.UI, OpenEyes.Util, EventEmitter2));