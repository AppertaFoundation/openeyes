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
(function(exports) {

	/**
	 * OpenEyes Util module
	 * @namespace OpenEyes.Util
	 * @memberOf OpenEyes
	 */
	var Util = {};

	/**
	 * Extend an objects' prototype with another objects' prototype.
	 * @method
	 * @param {Function} parent The parent constructor.
	 * @param {Function} child  The child constructor.
	 * @example
	 * function Parent() {}
	 * Parent.prototype.method = function() {};
	 *
	 * function Child() {}
	 * Util.inherits(Parent, Child);
	 *
	 * var child = new Child();
	 * child.method();
	 * @returns {Function} The child constructor.
	 */
	Util.inherits = function(parent, child) {
		child._super = parent;
		child.prototype = Object.create(parent.prototype);
		child.prototype.constructor = child;
		return child;
	};

    /**
     * Returns the next index of the data entries
     *
     * @param selector
     * @param dataKey
     * @returns {*}
     */
	Util.getNextDataKey = function(selector, dataKey){
        var keys = $(selector).map(function(i, el){
            return $(el).data(dataKey);
        }).get();

        if(keys.length) {
            return Math.max.apply(null, keys) + 1;
        } else {
            return 0;
        }
	};

    /**
     * Returns the given date in the format dd Month yyyy
     *
     * @param date
     * @returns {String}
     */
    Util.formatDateToDisplayString = function (date) {
        let raw_date = new Date(date);
        return raw_date.getDate() + ' ' +
          ['Jan', 'Feb', 'Mar', 'Apr',
           'May', 'Jun', 'Jul', 'Aug',
           'Sep', 'Oct', 'Nov', 'Dec'
          ][raw_date.getMonth()] + ' ' +
          raw_date.getFullYear();
    }

    Util.formatTimeToFuzzyDate = function (date) {
        let day = date.getDate();
        let displayDay = day < 10 ? '0' + day.toString() : day;
        let month = date.getMonth() + 1;
        let displayMonth = month < 10 ? '0' + month.toString() : month;
        let year = date.getFullYear();

        return year + '-' + displayMonth + '-' + displayDay;
    };

    /**
     * Get date from Fuzzy field set.
     *
     * @param fieldset
     * @returns {*}
     */
    Util.dateFromFuzzyFieldSet = function (fieldset) {
        let res = fieldset.find('select.fuzzy_year').val();
        let month = parseInt(fieldset.find('select.fuzzy_month option:selected').val());
        res += '-' + ((month < 10) ? '0' + month.toString() : month.toString());
        let day = parseInt(fieldset.find('select.fuzzy_day option:selected').val());
        res += '-' + ((day < 10) ? '0' + day.toString() : day.toString());

        return res;
    }

    /**
     * Get cookie by name.
     *
     * @param name
     * @returns {String}
     */
    Util.getCookie = function (name) {
        let cookie = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
        if (cookie) {
            return cookie[2];
        } else {
            return '';
        }
    }

	Util.createFormData = function(formData, key, data) {
		if (data === Object(data) || Array.isArray(data)) {
			for (var index in data) {
				Util.createFormData(formData, key + '[' + index + ']', data[index]);
			}
		} else {
			formData.append(key, data);
		}
	}


    /**
     * Equivalent of jquery's $(html), only using plain JavaScript
     *
     @param {String} html representing a single element
     @return {Element}
     */
    Util.htmlToElement = function(html) {
        const template = document.createElement('template');
        template.innerHTML = html.trim();
        return template.content.firstChild;
    }

	exports.Util = Util;

}(this.OpenEyes));
