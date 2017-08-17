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

(function(exports) {

    'use strict';

    function DiagnosesSearchController(options) {
        this.options = $.extend(true, {}, DiagnosesSearchController._defaultOptions, options);

        this.$inputField = this.options.inputField;
        this.code = this.options.code;
        this.commonlyUsedDiagnosesUrl = this.options.commonlyUsedDiagnosesUrl;
        this.single_template = this.options.single_template;

        this.init();
        this.initialiseAutocomplete();
        this.initialiseTriggers();
    }

    DiagnosesSearchController._defaultOptions = {
        'code': 'systemic',
     //   'selectedDiagnosesWrapper': '.enteredDiagnosisText',
        'inputField': $('.diagnoses-search-autocomplete'),
        'commonlyUsedDiagnosesUrl': '/disorder/getcommonlyuseddiagnoses/type/',
        'single_template' :
            "<div style=\"font-size: 13px; margin: 16px 0px 0px; display: none;\" class=\"enteredDiagnosisText panel diagnosis hidden\"></div>" +
            "<select class=\"commonly-used-diagnosis\"></select>" +
            "{{input_field}}" +
            "<input type=\"hidden\" name=\"DiagnosisSelection[disorder_id]\" class=\"savedDiagnosis\" value=\"\">"
            ,
    };
console.log(controller.$inputField.html());
    DiagnosesSearchController.prototype.init = function(){
        var controller = this;
        var $parent = controller.$inputField.parent();
        var url = controller.commonlyUsedDiagnosesUrl + controller.code;

        var html = Mustache.render(
            this.single_template,
            {'input_field': controller.$inputField.html()}
        );
console.log(html);
        $parent.html(html);
        /*$parent.prepend( $('<div>',{
            'style': 'font-size:13px; margin:16px 0 0 0;',
            'class': 'enteredDiagnosisText panel diagnosis hidden'}));*/

        $.getJSON(url, function(data){
           /* var $enteredWrapper = $parent.find('.enteredDiagnosisText');
            var $select = $('<select>',{'class': 'commonly-used-diagnosis'});

            $select.append( $('<option>',{'text': 'Select a commonly used diagnosis'}));
            $.each(data, function(i, item){
                $select.append( $('<option>',{'value': item.value, 'text': item.text}));
            });

            $enteredWrapper.after($select);

            $select.on('change', function(){
                $enteredWrapper.text( $(this).find('option:selected').text() );
                $enteredWrapper.append( $('<a>', {'class': 'clear-diagnosis-widget'}).text('(Remove)') );
                $parent.find('.savedDiagnosis').val( $(this).val() );
                $enteredWrapper.show();
                $(this).val('');
            });*/
        });
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
                    'success':function(data) {
                        data = $.parseJSON(data);
                        response(data);
                    }
                });
            },
            search: function () {},
            select: function(event, ui){
console.log(ui.item);
            },

            response: function (event, ui) {
            }
        });
    }

    DiagnosesSearchController.prototype.initialiseTriggers = function(){
        var controller = this;

        var $parent = controller.$inputField.parent();
        $parent.on('click', '.clear-diagnosis-widget', function(){
            console.log('asdsgfdgf');
            $parent.find('.enteredDiagnosisText').html('').hide();
        });

    }

    exports.DiagnosesSearchController = DiagnosesSearchController;

}(OpenEyes.UI));

$(document).ready(function() {

    $.each($('.diagnoses-search-autocomplete'), function(i, item){
        $(item).data('DiagnosesSearchController', new OpenEyes.UI.DiagnosesSearchController({'inputField': $(item) }) );
    });

});