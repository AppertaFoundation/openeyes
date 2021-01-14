/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Package or core JS functionality that is namespaced correctly for consistency with direction of travel
 * for JS functionality in OpenEyes. Introduced to supercede module.js as functionality from that is split
 * into appropriate controller behaviour for the elements. Code in here should be written so that it is always
 * included for any element use. The primary goal is to provide central singleton functions to manage interlinked
 * behaviour.
 */

var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};

(function(exports) {
    function DiagnosisCore()
    {
        this.diagnosesBySource = {};
        this.sourceIndex = 0;
    }

    /**
     * @todo: implement this function for greater flexibility when more elements need to track external diagnoses
     * @param element
     */
    DiagnosisCore.prototype.registerForSync = function(element)
    {
        // ideally elements that are to be updated with diagnoses should register for notifications
        // here. Not to be implemented immediately though.
    };

    /**
     * Look for elements that should be notified of diagnoses, and provide the latest set of diagnoses to them.
     */
    DiagnosisCore.prototype.sync = function()
    {
        // look for diagnosis element. if it exists, send it the current state of the data
        var diagnosesElement = $('#OphCiExamination_diagnoses');
        if (diagnosesElement.length) {
            var controller = diagnosesElement.data('controller');
            controller.setExternalDiagnoses(this.diagnosesBySource);
        }
    };

    /**
     * Track element sources with a unique id
     *
     * @param element
     * @returns {*|jQuery}
     */
    DiagnosisCore.prototype.getSourceId = function(element)
    {
        var id = $(element).data('diagnosis-core-id');
        if (id === undefined) {
            id = this.sourceIndex++;
            $(element).data('diagnosis-core-id', id);
        }
        return id;
    };

    /**
     * Singleton function for any element to call when wanting to update diagnoses that have been
     * found from user interaction.
     *
     * @param diagnoses
     * @param sourceElement
     */
    DiagnosisCore.prototype.setForSource = function(diagnoses, sourceElement)
    {
        var source = this.getSourceId(sourceElement);
        this.diagnosesBySource[source] = diagnoses;

        // run a sync
        this.sync();
    };

    exports.Diagnosis = new DiagnosisCore();


    // corresponds to the values used for eye records
    exports.eyeValues = {
        'right': 2,
        'left': 1

    };

    exports.beoEyeValues = {
        'right': 2,
        'left': 1,
        'beo': 4
    };

    exports.eyeValueHasEye = function(currentValue, eye)
    {
        if (!exports.beoEyeValues[eye] || !currentValue) {
            return false;
        }
        return (currentValue & exports.beoEyeValues[eye]) === exports.beoEyeValues[eye];
    };

    /**
     * Given the current bitwise value for eye selection, add the given eyeToAdd to it.
     * eyeToAdd must be a valid eye key @see Util.eyeValues
     *
     * @param {int} currentValue
     * @param {string} eyeToAdd
     * @param {boolean} includeBeo
     * @return {number|*}
     */
    exports.addEyeToEyeValue = function(currentValue, eyeToAdd, includeBeo) {
        const lookup = includeBeo ? exports.beoEyeValues : exports.eyeValues;

        if (lookup[eyeToAdd] !== undefined) {
            return currentValue | lookup[eyeToAdd];
        }
        return currentValue;
    };

    /**
     * Given the current bitwise value for eye selection, remove the given eyeToRemove from it.
     * eyeToRemove must be a valid eye key @see Util.eyeValues
     *
     * @param {int} currentValue
     * @param {string} eyeToRemove
     * @param {boolean} includeBeo
     * @return {number|*}
     */
    exports.removeEyeFromEyeValue = function(currentValue, eyeToRemove, includeBeo) {
        const lookup = includeBeo ? exports.beoEyeValues : exports.eyeValues;

        if (lookup[eyeToRemove] !== undefined) {
            if ((currentValue & lookup[eyeToRemove]) === lookup[eyeToRemove]) {
                return currentValue ^ lookup[eyeToRemove];
            }
        }
        return currentValue;
    };


})(OpenEyes.OphCiExamination);