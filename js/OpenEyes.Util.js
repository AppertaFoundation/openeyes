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

	var Util = {};

	/**
	 * Extends an object with another objects' properties.
	 * @name Object#mixin
	 */
	if (!Object.prototype.mixin) {
		Object.defineProperty(Object.prototype, 'mixin', {
			value: function(obj) {
				for (var prop in obj) {
					if (obj.hasOwnProperty(prop)) {
						this[prop] = obj[prop];
					}
				}
				return this;
			}
		});
	}

	/**
	 * Extend an objects' prototype with another objects' prototype.
	 * @name Function#inherits
	 */
	if (!Function.prototype.inherits) {
		Object.defineProperty(Function.prototype, 'inherits', {
			value: function(_super, _subProto) {
				this._super = _super;
				this.prototype = Object.create(_super.prototype);
				this.prototype.constructor = this;
				this.prototype.mixin(_subProto);
				return this;
			}
		});
	}

	/**
	 * Binds methods of an object to the object itself.
	 * @param {object} object - The object with the methods to bind.
	 * @param {boolean} [inherited=false] - Bind to inherited methods?
	 */
	Util.bindAll = function(object, inherited) {

		for(var key in object) {

			var isFunction = typeof object[key] === 'function';

			if ((inherited || object.hasOwnProperty(key)) && isFunction) {
				object[key] = object[key].bind(object);
			}
		}
	};

	exports.Util = Util;

}(this.OpenEyes));