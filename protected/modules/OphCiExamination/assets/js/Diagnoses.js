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

(function (exports) {

    /**
     *
     * @param options
     * @constructor
     */
    function DiagnosesController(options) {
        var controller = this;

        this.options = $.extend(true, {}, DiagnosesController._defaultOptions, options);

        this.$element = this.options.element;
        this.subspecialtyRefSpec = this.options.subspecialtyRefSpec;

        this.$table = this.$element.find('#OEModule_OphCiExamination_models_Element_OphCiExamination_Diagnoses_diagnoses_table');
        this.$noOphthalmicDiagnosesWrapper = this.$element.find('.OEModule_OphCiExamination_models_Element_OphCiExamination_Diagnoses_no_ophthalmic_diagnoses_wrapper');
        this.$noOphthalmicDiagnosesFld = this.$element.find('#OEModule_OphCiExamination_models_Element_OphCiExamination_Diagnoses_no_ophthalmic_diagnoses');

        this.loaderClass = this.options.loaderClass;
        this.$loader = this.$table.find('.' + this.loaderClass);

        this.templateText = this.$element.find('.entry-template').text();
        this.$popup = $('#ophthalmic-diagnoses-popup');
        this.externalDiagnoses = {};

        this.searchRequest = null;

        this.initialiseTriggers();
        this.initialiseDatepicker();
    }

    /**
     * Data structure containing all the configuration options for the controller
     * @private
     */
    DiagnosesController._defaultOptions = {
        'selector': '#OphCiExamination_diagnoses',
        addButtonSelector: '#add-ophthalmic-diagnoses',
        element: undefined,
        subspecialtyRefSpec: null,
        loaderClass: 'external-loader',
        code: '130',
        searchSource: '/disorder/autocomplete',
        selectOptions: '#ophthalmic-diagnoses-select-options',
        selectItems: '#ophthalmic-diagnoses-option',
        searchOptions: '.ophthalmic-diagnoses-search-options',
        searchInput: '#ophthalmic-diagnoses-search-field',
        searchResult: '#ophthalmic-diagnoses-search-results'
    };

    DiagnosesController.prototype.initialiseTriggers = function () {
        var controller = this;

        $(document).ready(function () {
            controller.toggleNoOphthalmicDiagnoses();
        });

        // removal button for table entries
        controller.$table.on('click', '.removeDiagnosis', function (e) {
            let glaucomaDiagnosisRemoved = false;
            let tableRow = $(e.target).parents('tr');
            let glaucomaDiagnosisSelectedEyes = [];

            if (tableRow.find('input[name^="glaucoma_diagnoses"]').filter('[value=true],[value="1"]').length) {
                let side_checked = tableRow.find('.oe-eye-lat-icons :checked');
                glaucomaDiagnosisRemoved = true;
                switch (side_checked.length) {
                    case 2:
                        glaucomaDiagnosisSelectedEyes['right-eye'] = true;
                        glaucomaDiagnosisSelectedEyes['left-eye'] = true;
                        break;
                    case 1:
                        glaucomaDiagnosisSelectedEyes[side_checked.data('eye-side') + '-eye'] = true;
                        break;
                }
            }
            e.preventDefault();
            tableRow.remove();
            controller.toggleNoOphthalmicDiagnoses();
            controller.toggleTable();


            $(":input[name^='glaucoma_diagnoses']").trigger('change', [
                '.pcrrisk_glaucoma',
                glaucomaDiagnosisRemoved,
                glaucomaDiagnosisSelectedEyes
            ]);

            controller.compareWithTriage();
        });

        controller.$element.on('click', '#ophthalmic-diagnoses-search-btn', function () {
            if ($(this).hasClass('selected')) {
                return;
            }

            $(this).addClass('selected');
            $('#ophthalmic-diagnoses-select-btn').removeClass('selected');

            $(controller.options.searchOptions).show();
            $(controller.options.selectOptions).find('.selected').removeClass('selected');
            $(controller.options.selectOptions).hide();
        });

        controller.$element.on('click', '#ophthalmic-diagnoses-select-btn', function () {
            if ($(this).hasClass('selected')) {
                return;
            }

            $(this).addClass('selected');
            $('#ophthalmic-diagnoses-search-btn').removeClass('selected');

            $(controller.options.selectOptions).show();
            $(controller.options.searchOptions).hide();
            $(controller.options.searchInput).val('');
            $(controller.options.searchResult).empty();
        });

        $(controller.options.searchInput).on('keyup', function () {
            controller.popupSearch();
        });

        controller.$element.on('change', 'select.condition-secondary-to', function () {
            var $option = $(this).find('option:selected'),
                type = $option.data('type'),
                row, $tr, item;
            $tr = $(this).closest('tr');

            let row_count = $tr.data('key');

            let row_values = [{
                id: $option.data('id'),
                label: $option.data('label'),
                eye_id: controller.getEyeIdFromRow($tr),
                is_principal: $tr.find('#principal_diagnosis_row_key').is(':checked') ? 1 : 0,
                date: $('#diagnoses-datepicker-' + row_count).val()
            }];

            if (type && type === 'alternate') {
                // select only the alternate
                // and only that one - instead of the first/main selected
                item = $option.data('id');

                if (item) {
                    $tr.remove();
                    row = controller.createRow(row_values);
                    controller.$table.find('tbody').append(row);
                    $tr = controller.$table.find('tbody tr:last');
                    controller.setDatepicker();
                }
            } else if (type && type === 'disorder') {
                // just add the disorder as an extra row
                row = controller.createRow([{ id: $option.data('id'), label: $option.data('label') }]);
                controller.$table.find('tbody').append(row);
            } else if (type && type === 'finding') {
                //Add Further Findings
                OphCiExamination_AddFinding($option.data('id'), $option.data('label'));
            }

            $(this).closest('.condition-secondary-to-wrapper').hide();
        });

        controller.$noOphthalmicDiagnosesFld.on('click', function () {

            if (controller.$noOphthalmicDiagnosesFld.prop('checked')) {
                controller.toggleTable();
                controller.$popup.hide();
            } else {
                controller.$popup.show();
            }
        });

        controller.$popup.on('click', function (e) {
            e.preventDefault();
        });

        eye_selector = new OpenEyes.UI.EyeSelector({
            element: controller.$element.closest('section')
        });

        DiagnosesController.prototype.toggleNoOphthalmicDiagnoses = function () {
            if (this.$noOphthalmicDiagnosesFld.prop('checked')) {
                this.$popup.hide();
            } else {
                this.$popup.show();
            }

            if (this.$table.find('.removeDiagnosis').length === 0) {
                this.$noOphthalmicDiagnosesWrapper.show();
                this.$table.hide();
            } else {
                this.$noOphthalmicDiagnosesWrapper.hide();
                this.$table.show();
            }
        };

        DiagnosesController.prototype.toggleTable = function () {
            if (this.$table.find('.removeDiagnosis').length === 0 && this.$table.find('.read-only').length === 0) {
                this.$table.hide();
            } else if (this.$table.find('.removeDiagnosis').length === 0 && this.$table.find('.read-only').length === 1) {
                this.$table.hide();
            } else {
                this.$table.show();
            }
        };
    };

    DiagnosesController.prototype.popupSearch = function () {
        var controller = this;
        if (controller.searchRequest !== null) {
            controller.searchRequest.abort();
        }
        controller.searchRequest = $.getJSON(controller.options.searchSource, {
            term: $(controller.options.searchInput).val(),
            code: controller.options.code,
            ajax: 'ajax'
        }, function (data) {
            controller.searchRequest = null;
            $(controller.options.searchResult).empty();
            var no_data = !$(data).length;
            $(controller.options.searchResult).toggle(!no_data);
            $('#ophthalmic-diagnoses-search-no-results').toggle(no_data);
            for (let i = 0; i < data.length; i++) {
                controller.appendToSearchResult(data[i]);
            }
        });
    };

    DiagnosesController.prototype.appendToSearchResult = function (item, is_selected) {
        let controller = this;
        let $span = "<span class='auto-width'>" + item.value + "</span>";
        let $item = $("<li>")
            .attr('data-str', item.value)
            .attr('data-id', item.id);
        if (is_selected) {
            $item.addClass('selected');
        }
        $item.append($span);
        $(controller.options.searchResult).append($item);
    };

    DiagnosesController.prototype.initialiseDatepicker = function () {
        var row_count = OpenEyes.Util.getNextDataKey(this.$element.find('table tbody tr'), 'key');
        for (var i = 0; i < row_count; i++) {
            var datepicker_name = '#diagnoses-datepicker-' + i;
            var datepicker = $(this.$table).find(datepicker_name);
            if (datepicker.length != 0) {
                pickmeup(datepicker_name, {
                    format: 'Y-m-d',
                    hide_on_select: true,
                    default_date: false
                });
            }
        }
    };

    DiagnosesController.prototype.setDatepicker = function () {
        var row_count = OpenEyes.Util.getNextDataKey(this.$element.find('table tbody tr'), 'key') - 1;
        var datepicker_name = '#diagnoses-datepicker-' + row_count;
        var datepicker = $(this.$table).find(datepicker_name);
        if (datepicker.length !== 0) {
            pickmeup(datepicker_name, {
                format: 'Y-m-d',
                hide_on_select: true,
                default_date: false
            });

            $(datepicker_name).val($.datepicker.formatDate('yy-mm-dd', new Date()));
        }
    };

    DiagnosesController.prototype.getEyeIdFromRow = function ($row) {
        let eye_id = $row.find('.js-left-eye').is(':checked') ? 1 : 0;
        eye_id += $row.find('.js-right-eye').is(':checked') ? 2 : 0;
        return eye_id;
    };

    DiagnosesController.prototype.createRow = function (selectedItems) {
        var newRows = [];
        var template = this.templateText;
        var element = this.$element;
        var row;

        for (var i in selectedItems) {

            if (typeof selectedItems[i].eye_id === 'undefined') {
                selectedItems[i].eye_id = null;
            }
            let data = {};
            data.row_count = OpenEyes.Util.getNextDataKey(element.find('table tbody tr'), 'key') + newRows.length;
            data.date = selectedItems[i].date;
            data.disorder_id = selectedItems[i].id;
            data.disorder_display = selectedItems[i].label;
            data.eye_id = selectedItems[i].eye_id;
            data.right_eye_checked = selectedItems[i].eye_id === 2 || selectedItems[i].eye_id === 3;
            data.left_eye_checked = selectedItems[i].eye_id === 1 || selectedItems[i].eye_id === 3;
            data.is_principal = selectedItems[i].is_principal;
            data.is_glaucoma = selectedItems[i].is_glaucoma;
            row = Mustache.render(template, data);
            newRows.push(row);
        }

        return newRows;
    };

    DiagnosesController.prototype.addEntry = function (selectedItems) {
        let controller = this;
        var rows = this.createRow(selectedItems);
        this.toggleTable();
        this.toggleNoOphthalmicDiagnoses();
        for (var i in rows) {
            this.$table.find('tbody').append(rows[i]);
            this.appendSecondaryDiagnoses(selectedItems[i].secondary, this.$table.find('tbody tr:last'), selectedItems[i].alternate);
            this.selectEye(this.$table.find('tbody tr:last'), selectedItems[i].eye_id);
            this.setDatepicker();
        }
        this.$table.find('.js-left-eye').off('click').on('click', function () { controller.compareWithTriage(); });
        this.$table.find('.js-right-eye').off('click').on('click', function () { controller.compareWithTriage(); });
        $(":input[name^='glaucoma_diagnoses']").trigger('change', ['bybys']);
    };

    DiagnosesController.prototype.appendSecondaryDiagnoses = function (secondary_diagnoses, $tr, alternate_diagnoses) {
        if (this.subspecialtyRefSpec === 'GL' && secondary_diagnoses !== undefined && secondary_diagnoses.length) {
            $tr.find('.condition-secondary-to-wrapper').show();
            let template = '<option data-id="{{id}}" data-label="{{label}}" data-type="{{type}}">{{label}}  </option>';
            let template_alternate = '<option data-id="{{id}}" data-label="{{label}}" data-type="{{type}}">{{selection_label}}  </option>';

            if (alternate_diagnoses !== undefined && alternate_diagnoses !== null) {
                data = {};
                data.label = alternate_diagnoses['label'];
                data.selection_label = alternate_diagnoses['selection_label'];
                data.id = alternate_diagnoses['id'];
                data.type = 'alternate';
                var select = Mustache.render(template_alternate, data);
                $tr.find('.condition-secondary-to').append(select);
            }

            for (var i in secondary_diagnoses) {
                data = {};
                data.label = secondary_diagnoses[i]['label'];
                data.id = secondary_diagnoses[i]['id'];
                data.type = secondary_diagnoses[i]['type'];
                var select = Mustache.render(template, data);
                $tr.find('.condition-secondary-to').append(select);
            }
        } else {
            $tr.find('.condition-secondary-to-wrapper').hide();
        }
    };

    DiagnosesController.prototype.selectEye = function ($tr, eye_id) {
        if (eye_id & 1) {
            $tr.find('.js-left-eye').prop('checked', true);
        }
        if (eye_id & 2) {
            $tr.find('.js-right-eye').prop('checked', true);
        }

        this.compareWithTriage();
    };

    /**
     * Set the external diagnoses and update the element display accordingly.
     *
     * @param diagnosesBySource
     */
    DiagnosesController.prototype.setExternalDiagnoses = function (diagnosesBySource) {
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
                        newExternalDiagnoses[diagnosis] = { sides: [diagnosesBySource[source][i][1]] };
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
    DiagnosesController.prototype.removeExternalDiagnosis = function (code) {
        this.$table.find('input[type="hidden"][value="' + code + '"]').closest('tr').remove();
    };

    /**
     * Runs through the current external diagnoses and ensures they are displayed correctly
     */
    DiagnosesController.prototype.renderExternalDiagnoses = function () {
        var controller = this;

        for (let diagnosisCode in controller.externalDiagnoses) {
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
    DiagnosesController.prototype.updateExternalDiagnosis = function (code, sides) {
        var controller = this;
        controller.retrieveDiagnosisDetail(code, controller.resolveEyeCode(sides), controller.setExternalDiagnosisDisplay.bind(controller));
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
    DiagnosesController.prototype.retrieveDiagnosisDetail = function (code, side, callback) {
        var controller = this;
        if (diagnosisDetail.hasOwnProperty(code)) {
            callback(diagnosisDetail[code].id, diagnosisDetail[code].name, side);
        } else {
            $.ajax({
                'type': 'GET',
                // TODO: this should be a property of the element
                'url': '/OphCiExamination/default/getDisorder?disorder_id=' + code,
                'beforeSend': function () {
                    controller.$loader.show();
                },
                'success': function (json) {
                    diagnosisDetail[code] = json;
                    callback(diagnosisDetail[code].id, diagnosisDetail[code].name, side);
                },
                'complete': function () {
                    controller.$loader.hide();
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
    DiagnosesController.prototype.resolveEyeCode = function (sides) {
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
    DiagnosesController.prototype.setExternalDiagnosisDisplay = function (id, name, side) {

        var controller = this;

        // code adapted from module.js to verify if diagnosis already in table or not
        var alreadyInList = false;
        var row, $tr;

        // iterate over table rows.
        $('#OphCiExamination_diagnoses').children('tr').each(function () {
            if ($(this).find('input[type=hidden][name*=\\[disorder_id\\]]').val() === id) {
                alreadyInList = true;
                // only want to alter sides for disorders that have been added from external source
                // at this point
                $(this).find('.js-left-eye').prop('checked', side === 3 || side === 1);
                $(this).find('.js-right-eye').prop('checked', side === 3 || side === 2);

                listSide = $(this).find('input[type="radio"]:checked').val();
                if (listSide !== side) {
                    $(this).find('input[type="radio"][value=' + id + ']').prop('checked', true);
                }
                // stop iterating
                return false;
            }
        });

        if (!alreadyInList) {
            // adding this disorder to the search result as createRow will check if there is any selected items in
            // selectItems or searchResult - otherwise it won't add
            controller.appendToSearchResult({ id: id, value: name }, true);
            controller.addEntry([{ id: id, label: name, eye_id: side }]);
            $tr = this.$table.find('tbody tr:last');
            $tr.addClass('js-external');
        } else {
            this.compareWithTriage();
        }
    };

    DiagnosesController.prototype.compareWithTriage = function (removeWarningOnly) {
        const triageEyeElement = $('.js-examination-triage-eye');

        $('.js-examination-diagnoses-triage-eye-warning').remove();

        if (!removeWarningOnly && triageEyeElement.length > 0 && triageEyeElement.val() !== undefined) {
            const controller = this;
            const triageEyeId = parseInt(triageEyeElement.val().trim());

            const rowEyeIds = $('#OphCiExamination_diagnoses > tr')
                .map(function () { return controller.getEyeIdFromRow($(this)); })
                .get()
                .filter((value) => value !== 0);

            const eyeShared = rowEyeIds.length === 0 || rowEyeIds.some((value) => (value & triageEyeId) !== 0);

            if (!eyeShared) {
                const warningDiv = $('<div>', { 'class': 'alert-box with-icon warning js-examination-diagnoses-triage-eye-warning' })
                    .text('Warning: Mismatch in the Laterality of Eye selected in all Diagnoses and Triage Elements.')

                $('#OphCiExamination_diagnoses').parents('div.element-fields').after(warningDiv);
            }
        }
    };

    exports.DiagnosesController = DiagnosesController;
})(OpenEyes.OphCiExamination);
