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

(function(exports) {

    /**
     *
     * @param options
     * options must be of the following structure:
     * {
     *     'name': 'Name of the form (will be used in the post request)',
     *     'tableSelector': 'The selector that selects the form table',
     *     'rowSelector': 'The selector that selects a row from the form table',
     *     'rowIdentifier': 'The data attribute on the row that represents the key',
     *     'structure': {
     *         'field path': 'selector for field',
     *         'MODEL_NAME[KEY][id]' : '#MODEL_NAME_KEY_id', <- example
     *         'MODEL_NAME[KEY][name]' : '', <- if the selector is left blank then the field path is used as the name attribute in the selector.
     *     }
     * }
     * @constructor
     */
    function GenericFormJSONConverter(options) {
        this.options = options;
    }

    /**
     * Recursively traverse linear attribute value paths and return the attribute 
     * as a structured object
     *
     * @param path Array of value paths
     * @param output Structured output array
     * @returns Array
     */
    GenericFormJSONConverter.prototype.traversePath = function (path, output) {
        if (path.length === 2) {
            output[path[0]] = path[1];
            return output;
        } else if (path.length > 2) {
            if (!(path[0] in output)) {
                output[path[0]] = {};
            }
            Object.assign(output[path[0]], this.traversePath(path.slice(1), output[path[0]]));
            return output;  
        }
    }

    /**
     * Find all inputs in an element and convert their data to a JSON string
     *
     * @param parentSelector Selector that selects the form table to be encoded
     * @returns String
     */
    GenericFormJSONConverter.prototype.JSONEncodeAttributes = function (parentSelector) {
        let json = {};
        $(this.options.tableSelector).find(this.options.rowSelector).each( (i, row) => {
            let rowIdentifier = $(row).data(this.options.rowIdentifier);
            if (!rowIdentifier && rowIdentifier !== 0) {
                return;
            }
            json[rowIdentifier] = {};
            Object.keys(this.options.structure).forEach( (fieldKey, i) => {
                let fieldSelector = (this.options.structure[fieldKey] === '') ?
                    '[name="' + fieldKey.replace('ROW_IDENTIFIER', rowIdentifier) + '"]' :
                    this.options.structure[fieldKey].replace('ROW_IDENTIFIER', rowIdentifier);
                let value = $(fieldSelector).val();
                let cleanValue = typeof(value) === 'string' ? value.replace(/'/g, '\\"') : value;

                let path = fieldKey.replace('[ROW_IDENTIFIER]', '').replace(/\]/g, '').split('[');
                path.push(cleanValue);
                json[rowIdentifier] = Object.assign(json[rowIdentifier], this.traversePath(path, json[rowIdentifier]));
            });
        });
        return JSON.stringify(json).replace(/"/g, "'");
    };

    /**
     * Disable regular attributes and inject input containing JSON string
     *
     * @param JSONString JSON string encoding of element attributes
     */
    GenericFormJSONConverter.prototype.substituteAttributesWithJSON = function (JSONString) {
        $(this.options.tableSelector).find('input, select, textarea').removeAttr('name');
        $(this.options.tableSelector).append('<input name="' + this.options.name + '[JSON_string]" type="hidden" value="' + JSONString + '" />');
    };

    /**
     * Reduce all inputs to single input with JSON string
     *
     * @param elementName Name of the element to be encoded
     */
    GenericFormJSONConverter.prototype.convert = function () {
        let JSON_attributes = this.JSONEncodeAttributes();
        this.substituteAttributesWithJSON(JSON_attributes);
    };

    exports.GenericFormJSONConverter = GenericFormJSONConverter;
}(this.OpenEyes));

