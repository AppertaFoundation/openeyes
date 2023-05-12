/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};

(function(exports) {

    /**
     *
     * @param options
     * @constructor
     */
    function ElementFormJSONConverter() {
    }

    /**
     * Recursively traverse linear attribute value paths and return the attribute 
     * as a structured object
     *
     * @param arr Array of value paths
     * @param out Structured output array
     * @returns Array
     */
    ElementFormJSONConverter.prototype.traverseAttribute = function (arr, out) {
        if (arr.length === 2) {
            out[arr[0]] = typeof(arr[1]) == 'string' ? arr[1].replace(/'/g, '\\"') : arr[1];
            return out;
        } else {
            if (!(arr[0] in out)) {
                out[arr[0]] = {};
            }
            Object.assign(out[arr[0]], this.traverseAttribute(arr.slice(1), out[arr[0]]));
            return out;  
        }
    };

    /**
     * Find all inputs in an element and convert their data to a JSON string
     *
     * @param elementName Name of the element to be encoded
     * @returns String
     */
    ElementFormJSONConverter.prototype.JSONEncodeAttributes = function (elementName) {
        let output = {};
        $('#'+ elementName).find('input, select, textarea').each( (i, input) => {
            let splitInput = input.name.split('[').map(e => e.slice(0,-1)).slice(1);
            splitInput.push(input.value);
            if ( //Special cases that need to be handled
                (input.name.includes('JSON_string')) ||
                (input.name.includes('prescribe') && !input.checked) ||
                (input.name.includes('dose_unit_term') && $(input).attr('disabled') === "disabled")
            ) {
                return;
            }
            if (splitInput.length > 1) {
                output = this.traverseAttribute(splitInput, output);
            }
        });
        return JSON.stringify(output).replace(/"/g, "'");
    };

    /**
     * Disable regular attributes and inject input containing JSON string
     *
     * @param elementName Name of the element to be encoded
     * @param JSONString JSON string encoding of element attributes
     */
    ElementFormJSONConverter.prototype.substituteAttributesWithJSON = function (elementName, JSONString) {
        $('#' + elementName).find('input, select, textarea').addClass('js-json-serialized').attr('form', 'medication-management');
        let jsonInputName = `${elementName.replace('_element','')}[JSON_string]`;

        let jsonInput = $(`#${elementName} > input[name="${jsonInputName}"]`);

        if (jsonInput.length == 0) {
            $('#' + elementName).append(`<input name="${jsonInputName}" type="hidden" value="${JSONString}"/>`).removeAttr("form");
        } else {
            jsonInput.val(JSONString).removeAttr("form");
        }
    };

    /**
     * Reduce all inputs to single input with JSON string
     *
     * @param elementName Name of the element to be encoded
     */
    ElementFormJSONConverter.prototype.convert = function (elementName) {
        let JSON_attributes = this.JSONEncodeAttributes(elementName);
        this.substituteAttributesWithJSON(elementName, JSON_attributes);
    };

    exports.ElementFormJSONConverter = ElementFormJSONConverter;
})(OpenEyes.OphCiExamination);
