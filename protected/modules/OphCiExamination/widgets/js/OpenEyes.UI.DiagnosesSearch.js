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
        modelName: 'OEModule_OphCiExamination_models_SystemicDiagnoses',
        code: 'systemic',
        commonlyUsedDiagnosesUrl: '/disorder/getcommonlyuseddiagnoses/type/',
        renderTemplate: true,
        singleTemplate :
            "<div style='margin: 0px 0px 0px; display: none;' class='enteredDiagnosisText panel diagnosis hidden'></div>" +
            "<select class='commonly-used-diagnosis'></select>" +
            "{{{input_field}}}" +
            "<input type='hidden' name='OEModule_OphCiExamination_models_SystemicDiagnoses[id][]' class='savedDiagnosisId' value=''>" +
            "<input type='hidden' name='OEModule_OphCiExamination_models_SystemicDiagnoses[disorder_id][]' class='savedDiagnosis' value=''>"
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
                {'input_field': controller.$inputField.prop("outerHTML")}
            );

            $parent.html(html);
            controller.$inputField = controller.$row.find('.diagnoses-search-inputfield');

            $.getJSON(url, function(data){

                var $select = $parent.find('.commonly-used-diagnosis');

                $select.append( $('<option>',{'text': 'Select a commonly used diagnosis'}));
                $.each(data, function(i, item){
                    $select.append( $('<option>',{'value': item.value, 'text': item.text}));
                });

                controller.$row.find('.enteredDiagnosisText').after($select);
            });
        }

        savedDiagnoses = controller.$inputField.data('saved-diagnoses');

        if(savedDiagnoses && savedDiagnoses.length){

            $.each(savedDiagnoses, function(i, diagnosis){
                controller.$row.find('.enteredDiagnosisText').append(diagnosis.name);
                controller.$row.find('.savedDiagnosisId').val(diagnosis.id);
                controller.$row.find('.savedDiagnosis').val(diagnosis.value);
                controller.$row.find('.enteredDiagnosisText').append( $('<a>', {"class":"clear-diagnosis-widget", href:"javascript:void(0)"}).text("(Remove)"));
            });

            controller.$row.find('.enteredDiagnosisText').show();
        }

    }

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
                console.log(ui.item);
            },
            response: function (event, ui) {
                controller.$inputField.removeClass('inset-loader');
            }
        });
    }

    DiagnosesSearchController.prototype.initialiseTriggers = function(){
        var controller = this;
        var $parent = controller.$inputField.parent();

        controller.$row.on('click', '.clear-diagnosis-widget', function(){
            $(this).closest('.enteredDiagnosisText').html('').hide();
        });

        controller.$row.on('change', 'select.commonly-used-diagnosis', function(){

            var $enteredWrapper = $(this).closest('td').find('.enteredDiagnosisText');

            $enteredWrapper.text( $(this).find('option:selected').text() );
            $enteredWrapper.append( $('<a>', {'class': 'clear-diagnosis-widget'}).text('(Remove)') );
            controller.$row.find('.savedDiagnosis').val( $(this).val() );
            $enteredWrapper.show();
            $(this).val('');
        });

    }

    exports.DiagnosesSearchController = DiagnosesSearchController;

}(OpenEyes.UI));