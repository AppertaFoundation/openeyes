/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

var OpenEyes = OpenEyes || {};

OpenEyes.UI = OpenEyes.UI || {};

(function (exports) {

    'use strict';

    function DiagnosesSearchController(options) {
        this.options = $.extend(true, {}, DiagnosesSearchController._defaultOptions, options);

        this.$inputField = this.options.inputField;
        this.fieldPrefix = this.options.fieldPrefix;
        this.$row = this.options.inputField.closest('tr');
        this.code = this.options.code;
        this.row_key = this.options.row_key;
        this.renderCommonlyUsedDiagnoses = this.options.renderCommonlyUsedDiagnoses;
        this.commonlyUsedDiagnosesUrl = this.options.commonlyUsedDiagnosesUrl;
        this.singleTemplate = this.options.singleTemplate;
        this.renderTemplate = this.options.renderTemplate;
        this.renderSecondaryTo = this.options.renderSecondaryTo;
        this.subspecialtyRefSpec = this.options.subspecialtyRefSpec;

        this.init();
        this.initialiseAutocomplete();
        this.initialiseTriggers();
    }

    DiagnosesSearchController._defaultOptions = {
        fieldPrefix: 'fieldPrefixDefault',
        code: 'systemic',
        renderCommonlyUsedDiagnoses: true,
        commonlyUsedDiagnosesUrl: '/disorder/getcommonlyuseddiagnoses/type/',
        renderTemplate: true,
        singleTemplate :
        "<span class='medication-display' style='display:none'>" +
        "<a href='javascript:void(0)' class='diagnosis-rename'><i class='oe-i remove-circle small' aria-hidden='true' title='Change diagnosis'></i></a> " +
        "<span class='diagnosis-name'></span></span>" +
        "<select class='commonly-used-diagnosis cols-full'></select>" +
        "{{#render_secondary_to}}" +
        "<div class='condition-secondary-to-wrapper' style='display:none;'>" +
        "<div style='margin-top:7px;border-top:1px solid lightgray;padding:3px'>Associated diagnosis:</div>" +
        "<select class='condition-secondary-to'></select>" +
        "</div>" +
        "{{/render_secondary_to}}" +
        "{{{input_field}}}" +
        "<input type='hidden' name='{{field_prefix}}[id][]' class='savedDiagnosisId' value=''>" +
        "<input type='hidden' name='{{field_prefix}}[disorder_id][]' class='savedDiagnosis' value=''>",
        subspecialtyRefSpec: null,
        renderSecondaryTo: true
    };

    DiagnosesSearchController.prototype.init = function () {
        var controller = this;
        var $parent = controller.$inputField.parent();
        var url = controller.commonlyUsedDiagnosesUrl + controller.code;
        controller.$inputField.addClass('diagnoses-search-inputfield');
        var savedDiagnoses;

        if (controller.renderTemplate === true) {
            var html = Mustache.render(
                this.singleTemplate,
                {
                    'input_field': controller.$inputField.prop("outerHTML"),
                    'row_count': OpenEyes.Util.getNextDataKey($('#' + controller.fieldPrefix + '_diagnoses_table').find('tbody tr'), 'key'),
                    'field_prefix': controller.fieldPrefix,
                    'render_secondary_to': controller.renderSecondaryTo,
                }
            );

            $parent.html(html);
            controller.$inputField = controller.$row.find('.diagnoses-search-inputfield');

            if (controller.renderCommonlyUsedDiagnoses === true) {
                $.getJSON(url, function (data) {
                    var $select = $parent.find('.commonly-used-diagnosis');

                    $select.append($('<option>', {'text': 'Select a commonly used diagnosis'}));
                    $select.append($('<option>', {'text': '----------', 'disabled': 'disabled'}));
                    $.each(data, function (i, item) {
                        $select.append($('<option>', {'value': item.id, 'text': item.label, 'data-item': JSON.stringify(item)}));
                    });
                    controller.$inputField.before($select);
                });
            }
        }

        savedDiagnoses = controller.$inputField.data('saved-diagnoses');

        if(typeof savedDiagnoses === "string" && savedDiagnoses){
            savedDiagnoses = JSON.parse(savedDiagnoses);
        }

        if (savedDiagnoses && savedDiagnoses.disorder_id) {
            controller.addDiagnosis(savedDiagnoses.id, {label: savedDiagnoses.name, id: savedDiagnoses.disorder_id});
        }
    };

    /**
     * Diagnosis selected for the row
     *
     * @param id
     * @param item
     */
    DiagnosesSearchController.prototype.addDiagnosis = function(id, item){
        var controller = this;
        var $displayDiagnosis = controller.$row.find('.diagnosis-display');
        controller.$row.find('.diagnosis-name').text(item.label);

        $displayDiagnosis.show();

        //This is not the disorder's ID but the "row" or "entry" ID (SystemicDiagnoses_Diagnosis model)
        // if there is no ID this will be a brand new entry
        if(id){
            controller.$row.find('.savedDiagnosisId').val(id);
        }

        controller.updatePatientConditions(item);
        //This will be the actual disorder ID - SNOMED code
        controller.$row.find('.savedDiagnosis').val(item.id);

        controller.$row.find('.commonly-used-diagnosis').hide();
        controller.$row.find('.diagnoses-search-inputfield').hide();
        controller.$row.find('.medication-display').show();

        //Glaucoma special
        if(controller.subspecialtyRefSpec === 'GL' && controller.renderSecondaryTo === true){

            var $select = controller.$row.find('.commonly-used-diagnosis');
            var item = $select.find('option:selected').data('item');
            var $associated_select = controller.$row.find('.condition-secondary-to');

            if(item && item.hasOwnProperty('secondary') && item['secondary'].length > 0){

                $associated_select.append( $('<option>',{'text': 'Select'}));

                if(item['alternate']){
                    $associated_select.append( $('<option>',
                        {
                            'value': item['alternate'].id,
                            'text': item['alternate'].selection_label,
                            'data-type': 'alternate',
                            'data-item': JSON.stringify(item['alternate'])
                        }));
                }

                $.each(item['secondary'], function(i, item){
                    $associated_select.append( $('<option>',{'value': item.id, 'text': item.label, 'data-type': item.type }));
                });

                controller.$row.find('.condition-secondary-to-wrapper').show();
            } else {
                controller.$row.find('.condition-secondary-to-wrapper').hide();
            }


        }

        if(typeof controller.options.afterSelect === 'function'){
            controller.options.afterSelect.call(controller);
        }
    };

    /**
     * Check the given disorder item for any condition attributes that should be updated on the page
     */
    DiagnosesSearchController.prototype.updatePatientConditions = function(item)
    {
        // note that at the moment this follows the simple premise of inserting or removing additional
        // hidden fields that are universally searched for on the page, following the pattern established
        // by the original diagnoses element.
        if (item.is_diabetes) {
            this.$row.prepend('<input type="hidden" name="diabetic_diagnoses[]" value="1" /> ');
        } else {
            this.$row.find('input[name^="diabetic_diagnoses"]').remove();
        }

        // glaucoma is unlikely to come up given this is only being used for systemic disorders
        // but it is included for completeness.
        if (item.is_glaucoma) {
            this.$row.append('<input type="hidden" name="glaucoma_diagnoses[]" value="1" /> ').trigger('change');
        } else {
            this.$row.find('input[name^="glaucoma_diagnoses"]').remove();
        }

        // trigger event change for any controls looking for them
        $(":input[name^='diabetic_diagnoses']").trigger('change');
        $(":input[name^='glaucoma_diagnoses']").trigger('change');
    };


    DiagnosesSearchController.prototype.initialiseAutocomplete = function(){
        var controller = this;
    };

    DiagnosesSearchController.prototype.initialiseTriggers = function(){
        var controller = this;
        var $parent = controller.$inputField.parent();

        controller.$row.on('click', '.diagnosis-rename', function(){
            if(controller.renderCommonlyUsedDiagnoses){
                controller.$row.find('.commonly-used-diagnosis').show();

            }
            controller.$row.find('.diagnoses-search-inputfield').show();
            $(this).closest('.medication-display').hide();
            controller.$row.find('.condition-secondary-to-wrapper').hide();

            controller.$row.find('.savedDiagnosisId').val('');
            controller.$row.find('.savedDiagnosis').val('');
            controller.$inputField.val('');
            controller.$inputField.focus();
        });

        controller.$row.on('change', 'select.commonly-used-diagnosis', function(){
            controller.addDiagnosis(null, $(this).find('option:selected').data('item') );
            $(this).val('');
        });
    };

    exports.DiagnosesSearchController = DiagnosesSearchController;

}(OpenEyes.UI));