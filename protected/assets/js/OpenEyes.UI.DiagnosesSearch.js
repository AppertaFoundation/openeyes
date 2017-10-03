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
        this.commonlyUsedDiagnosesUrl = this.options.commonlyUsedDiagnosesUrl;
        this.singleTemplate = this.options.singleTemplate;
        this.renderTemplate = this.options.renderTemplate;

        this.init();
        this.initialiseAutocomplete();
        this.initialiseTriggers();
    }

    DiagnosesSearchController._defaultOptions = {
        fieldPrefix: 'fieldPrefixDefault',
        code: 'systemic',
        commonlyUsedDiagnosesUrl: '/disorder/getcommonlyuseddiagnoses/type/',
        renderTemplate: true,
        singleTemplate :
            "<span class='medication-display' style='display:none'>" + "<a href='javascript:void(0)' class='diagnosis-rename'><i class='fa fa-times-circle' aria-hidden='true' title='Change diagnosis'></i></a> " +
            "<span class='diagnosis-name'></span></span>" +
            "<select class='commonly-used-diagnosis'></select>" +
            "{{{input_field}}}" +
            "<input type='hidden' name='{{field_prefix}}[id][]' class='savedDiagnosisId' value=''>" +
            "<input type='hidden' name='{{field_prefix}}[disorder_id][]' class='savedDiagnosis' value=''>"
    };


    DiagnosesSearchController.prototype.init = function(){
        var controller = this;
        var $parent = controller.$inputField.parent();
        var url = controller.commonlyUsedDiagnosesUrl + controller.code;
        controller.$inputField.addClass('diagnoses-search-inputfield');
        var savedDiagnoses;

        if( controller.renderTemplate === true ){
            var html = Mustache.render(
                this.singleTemplate,
                {
                    'input_field': controller.$inputField.prop("outerHTML"),
                    'row_count': OpenEyes.Util.getNextDataKey( $('#' + controller.fieldPrefix + '_diagnoses_table').find('tbody tr'), 'key'),
                    'field_prefix' : controller.fieldPrefix
                }
            );

            $parent.html(html);
            controller.$inputField = controller.$row.find('.diagnoses-search-inputfield');

            $.getJSON(url, function(data){

                var $select = $parent.find('.commonly-used-diagnosis');

                $select.append( $('<option>',{'text': 'Select a commonly used diagnosis'}));
                $.each(data, function(i, item){
                    $select.append( $('<option>',{'value': item.id, 'text': item.label, 'data-item': JSON.stringify(item)}));
                });

                controller.$inputField.before($select);
            });
        }

        savedDiagnoses = controller.$inputField.data('saved-diagnoses');

        if(savedDiagnoses){
            controller.addDiagnosis(savedDiagnoses.id, {label: savedDiagnoses.name, id: savedDiagnoses.disorder_id} );
        }
    }

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
            this.$row.append('<input type="hidden" name="diabetic_diagnoses[]" value="1" /> ');
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

        controller.$inputField.autocomplete({
            minLength: 2,
            delay: 700,
            source: function (request, response) {
                $.ajax({
                    'url': '/disorder/autocomplete',
                    'type':'GET',
                    'data':{'term': request.term, 'code': controller.code},
                    'beforeSend': function(){
                    },
                    'success':function(data) {
                        data = $.parseJSON(data);
                        response(data);
                    }
                });
            },
            search: function () {
                controller.$inputField.addClass('inset-loader');
            },
            select: function(event, ui){
                //no multiple option
                controller.addDiagnosis(null, ui.item);

                //clear input
                $(this).val("");
                return false;
            },
            response: function (event, ui) {
                controller.$inputField.removeClass('inset-loader');
            }
        });
    };

    DiagnosesSearchController.prototype.initialiseTriggers = function(){
        var controller = this;
        var $parent = controller.$inputField.parent();

        controller.$row.on('click', '.diagnosis-rename', function(){
            controller.$row.find('.commonly-used-diagnosis').show();
            controller.$row.find('.diagnoses-search-inputfield').show();
            $(this).closest('.medication-display').hide();
        });

        controller.$row.on('change', 'select.commonly-used-diagnosis', function(){
            controller.addDiagnosis(null, $(this).find('option:selected').data('item') );
            $(this).val('');
        });
    }

    exports.DiagnosesSearchController = DiagnosesSearchController;

}(OpenEyes.UI));