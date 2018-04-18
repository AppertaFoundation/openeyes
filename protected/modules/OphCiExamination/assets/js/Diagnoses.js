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

var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};

(function(exports) {

    /**
     *
     * @param options
     * @constructor
     */
    function DiagnosesController(options)
    {
        var controller = this;

        this.options = $.extend(true, {}, DiagnosesController._defaultOptions, options);

        this.$element = this.options.element;
        this.subspecialtyRefSpec = this.options.subspecialtyRefSpec;

        this.$table = this.$element.find('table');
        this.templateText = this.$element.find('.entry-template').text();
        this.externalDiagnoses = {};

        this.initialiseTriggers();
    }

    /**
     * Data structure containing all the configuration options for the controller
     * @private
     */
    DiagnosesController._defaultOptions = {
        'selector': '#OphCiExamination_diagnoses',
        addButtonSelector: '.add-entry',
        element: undefined,
        subspecialtyRefSpec: null
    };

    DiagnosesController.prototype.initialiseTriggers = function()
    {
        var controller = this;

        // removal button for table entries
        controller.$table.on('click', '.button.remove', function(e) {
            e.preventDefault();
            $(e.target).parents('tr').remove();
        });

        // setup current table row behaviours
        controller.$table.find('tbody tr').each(function() {
            controller.initialiseRow($(this));
        });

        // adding entries
        controller.$element.on('click', controller.options.addButtonSelector, function(e) {
            e.preventDefault();
            controller.addEntry();
        });

        controller.$element.on('change', 'select.condition-secondary-to', function(){
            var $option = $(this).find('option:selected'),
                type = $option.data('type'),
                row;

            if(type && type === 'alternate'){
                // select only the alternate
                // and only that one - instead of the first/main selected
                var $tr = $(this).closest('tr'),
                    item = $tr.find('.commonly-used-diagnosis option:selected').data('item');

                if(item && item['alternate']){
                    row = controller.createRow({disorder_id: item['alternate'].id, disorder_display:item['alternate'].label});
                    controller.$table.find('tbody').append(row);
                    controller.initialiseRow(controller.$table.find('tbody tr:last'));
                }
            } else if(type && type === 'disorder'){
                // just add the disorder as an extra row
                row = controller.createRow({disorder_id: $(this).val(), disorder_display:$option.text()});
                controller.$table.find('tbody').append(row);
                controller.initialiseRow(controller.$table.find('tbody tr:last'));
            } else if(type && type === 'finding') {
                //Add Further Findings
                OphCiExamination_AddFinding($(this).val(), $option.text());
            }

            $(this).closest('.condition-secondary-to-wrapper').hide();
        });

    };

    DiagnosesController.prototype.initialiseRow = function($row)
    {
        var controller = this;
        var DiagnosesSearchController = null;
        var $radioButtons = $row.find('.sides-radio-group');

        $row.on('change', '.fuzzy-date select', function(e) {
            var $fuzzyFieldset = $(this).closest('fieldset');
            var date = controller.dateFromFuzzyFieldSet($fuzzyFieldset);
            $fuzzyFieldset.find('input[type="hidden"]').val(date);
        });

        DiagnosesSearchController = new OpenEyes.UI.DiagnosesSearchController({
            'inputField': $row.find('.diagnoses-search-autocomplete'),
            'fieldPrefix': $row.closest('section').data('element-type-class'),
            'code': "130",
            'afterSelect': function(){
                //Adding new element to array doesn't trigger change so do it manually
                $(":input[name^='diabetic_diagnoses']").trigger('change');
                $(":input[name^='glaucoma_diagnoses']").trigger('change');
            },
            'subspecialtyRefSpec': controller.subspecialtyRefSpec,
        });
        $row.find('.diagnoses-search-autocomplete').data('DiagnosesSearchController', DiagnosesSearchController );

        // radio buttons
        $radioButtons.on('change', 'input', function(){
            $(this).closest('tr').find('.diagnosis-side-value').val( $(this).val() );
        });
    }

    DiagnosesController.prototype.dateFromFuzzyFieldSet = function(fieldset)
    {
        var res = fieldset.find('select.fuzzy_year').val();
        var month = parseInt(fieldset.find('select.fuzzy_month option:selected').val());
        res += '-' + ((month < 10) ? '0' + month.toString() : month.toString());
        var day = parseInt(fieldset.find('select.fuzzy_day option:selected').val());
        res += '-' + ((day < 10) ? '0' + day.toString() : day.toString());

        return res;
    };

    DiagnosesController.prototype.createRow = function(data)
    {
        if (data === undefined)
            data = {};

        data['row_count'] = OpenEyes.Util.getNextDataKey( this.$element.find('table tbody tr'), 'key');

        return Mustache.render(
            this.templateText,
            data
        );
    };

    DiagnosesController.prototype.addEntry = function()
    {
        var row = this.createRow();
        this.$table.find('tbody').append(row);
        this.initialiseRow(this.$table.find('tbody tr:last'));
    };

    /**
     * Set the external diagnoses and update the element display accordingly.
     *
     * @param diagnosesBySource
     */
    DiagnosesController.prototype.setExternalDiagnoses = function(diagnosesBySource)
    {
        var controller = this;

        // reformat to controller usable structure
        var newExternalDiagnoses = {};
        for (var source in diagnosesBySource) {
            if (diagnosesBySource.hasOwnProperty(source)) {
                for (var i = 0; i < diagnosesBySource[source].length; i++) {
                    var diagnosis = diagnosesBySource[source][i][0];
                    if (diagnosesBySource[source][i][0] in newExternalDiagnoses) {
                        if (!(diagnosesBySource[source][i][1] in newExternalDiagnoses)) {
                            newExternalDiagnoses[diagnosis].sides.push(diagnosesBySource[source][i][1]);
                        }
                    } else {
                        newExternalDiagnoses[diagnosis] = {sides: [diagnosesBySource[source][i][1]]}
                    }
                }
            }
        }

        // check for external diagnoses that should be removed
        for (var code in controller.externalDiagnoses) {
            if (controller.externalDiagnoses.hasOwnProperty(code)) {
                if (!(code in newExternalDiagnoses)) {
                    controller.removeExternalDiagnosis(code);
                }
            }
        }

        // assign private property
        controller.externalDiagnoses = newExternalDiagnoses;
        // update display
        controller.renderExternalDiagnoses();
    };

    /**
     * Remove the diagnosis if it was added from an external source.
     */
    DiagnosesController.prototype.removeExternalDiagnosis = function(code)
    {
        this.$element.find('.external a.removeDiagnosis[rel="'+code+'"]').click();
    };

    /**
     * Runs through the current external diagnoses and ensures they are displayed correctly
     */
    DiagnosesController.prototype.renderExternalDiagnoses = function()
    {
        var controller = this;

        for (diagnosisCode in controller.externalDiagnoses) {
            if (controller.externalDiagnoses.hasOwnProperty(diagnosisCode)) {
                this.updateExternalDiagnosis(diagnosisCode, controller.externalDiagnoses[diagnosisCode].sides);
            }
        }
    };

    /**
     * Update the given diagnosis to apply to sides
     *
     * @param code
     * @param sides
     */
    DiagnosesController.prototype.updateExternalDiagnosis = function(code, sides)
    {
        var controller = this;
        controller.retrieveDiagnosisDetail(code, controller.resolveEyeCode(sides), controller.setExternalDiagnosisDisplay.bind(controller) );
    };

    var diagnosisDetail = {};

    /**
     * This will retrieve the diagnosis detail via ajax (if it's not already been retrieved)
     * and then pass the information to the given callback. The callback function should expect
     * to receive args [diagnosisId, diagnosisName, sideId]
     * @param code
     * @param sides
     * @param callback
     */
    DiagnosesController.prototype.retrieveDiagnosisDetail = function(code, side, callback)
    {
        if (diagnosisDetail.hasOwnProperty(code)) {
            callback(diagnosisDetail[code].id, diagnosisDetail[code].name, side);
        } else {
            $.ajax({
                'type': 'GET',
                // TODO: this should be a property of the element
                'url': '/OphCiExamination/default/getDisorder?disorder_id='+code,
                'success': function(json) {
                    diagnosisDetail[code] = json;
                    callback(diagnosisDetail[code].id, diagnosisDetail[code].name, side);
                }
            });
        }
    };

    /**
     * Expecting array of side values where 0 is right and 1 is left
     * If both are present returns 3 (both)
     * otherwise returns 2 for right and 1 for left
     * or undefined if no meaningful value is provided
     *
     * @param sides
     */
    DiagnosesController.prototype.resolveEyeCode = function(sides)
    {
        var left = false;
        var right = false;
        for (var i = 0; i < sides.length; i++) {
            if (sides[i] === 0)
                right = true;
            if (sides[i] === 1)
                left = true;
        }

        return right ? (left ? 3 : 2) : (left ? 1 : undefined);
    };

    /**
     * Check for the diagnosis in the current diagnosis element. If it's there, and is external, update the side.
     * If it's not, add it to the table.
     *
     * @param id
     * @param name
     * @param side
     */
    DiagnosesController.prototype.setExternalDiagnosisDisplay = function(id, name, side)
    {

        var controller = this;

        // code adapted from module.js to verify if diagnosis already in table or not
        var alreadyInList = false;
        var listSide = null;
        var row, $tr;
        var date = new Date();

        // iterate over table rows.
        $('#OphCiExamination_diagnoses').children('tr').each(function() {
            if ($(this).find('input[type=hidden][name*=\\[disorder_id\\]\\[\\]]').val() == id) {
                alreadyInList = true;
                if ($(this).hasClass('external')) {
                    // only want to alter sides for disorders that have been added from external source
                    // at this point
                    listSide = $('input[type="radio"]:checked').val();
                    if (listSide != side) {
                        // the
                        $(this).find('input[type="radio"][value=' + side + ']').prop('checked', true);
                    }
                }
                // stop iterating
                return false;
            }
        });

        if (!alreadyInList) {
            // TODO: this should be a method on this controller, but we're leveraging existing code for now.
            // NOTE: the hardcoded settings for diabetic/glaucoma flags are present to allow us to provide
            // the auto flag for control of removing diagnoses as the eyedraws are updated
            //OphCiExamination_AddDiagnosis(id, name, side, false, false, true);

            row = controller.createRow({disorder_id: id, disorder_display: name, eye_id:side});
            controller.$table.find('tbody').append(row);
            $tr = this.$table.find('tbody tr:last');
            controller.initialiseRow($tr);
            $tr.find('.sides-radio-group input[value="' + side + '"]').prop("checked", true);

            $tr.find('.fuzzy_day').val(date.getDate());
            $tr.find('.fuzzy_month').val(date.getMonth()+1);
            $tr.find('.fuzzy_year').val(date.getFullYear());
        }
    };


    exports.DiagnosesController = DiagnosesController;
})(OpenEyes.OphCiExamination);