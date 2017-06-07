/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
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
     * @param source
     */
    DiagnosisCore.prototype.setForSource = function(diagnoses, sourceElement)
    {
        source = this.getSourceId(sourceElement);
        if (!(source in this.diagnosesBySource)) {
            this.diagnosesBySource[source] = Array();
        }
        var removedDiagnoses = Array();
        for (var i = 0; i < this.diagnosesBySource[source].length; i++) {
            var diagnosis = this.diagnosesBySource[source][i];
            if (!diagnosis in diagnoses) {
                removedDiagnoses.push(diagnosis)
            }
        }
        this.diagnosesBySource[source] = diagnoses;
        if (removedDiagnoses.length > 0)
            this.removeDiagnosesFromSource(removedDiagnoses, source);

        // run a sync
        this.sync();
    };

    /**
     * This function may be entirely unnecessary depending on how much work we can do with Diagnosis element control
     *
     * @param diagnoses
     * @param removeSource
     */
    DiagnosisCore.prototype.removeDiagnosesFromSource = function(diagnoses, removeSource)
    {
        var dontRemove = Array();
        for (source in this.diagnosesBySource) {
            if (source === removeSource)
                continue;

            if (this.diagnosesBySource.hasOwnProperty(source)) {
                for (var i = 0; i < this.diagnosesBySource[source]; i++) {
                    var otherSourceDiagnosis = this.diagnosesBySource[source][i];
                    if (otherSourceDiagnosis in diagnoses) {
                        dontRemove.push(otherSourceDiagnosis);
                    }
                }
            }
        }
        // sync the arrays

        // do the remove


        // run a sync
    };

    exports.Diagnosis = new DiagnosisCore();

})(OpenEyes.OphCiExamination);