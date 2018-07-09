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

    function InputFieldValidationController(options) {
        this.options = $.extend(true, {}, InputFieldValidationController._defaultOptions, options);
        this.elementSelector = this.options.elementSelector;

        this.initialiseTriggers();
    }

    InputFieldValidationController._defaultOptions = {
        elementSelector: 'body'
    };

    InputFieldValidationController.prototype.initialiseTriggers = function(){
        var controller = this;

        $(this.elementSelector).on('keydown', '.input-validate', function(e){

            if(e.key === 'Backspace' || e.key === 'ArrowLeft' || e.key === 'ArrowRight' || e.key === 'Delete' || e.key === 'Tab'){
                return true;
            }

            var classList = $(this).attr('class').split(/\s+/);
            var validator = controller.getValidationByClass($(this).val(), classList);

            if(validator && !validator.test(e.key)){
                e.preventDefault();
                e.returnValue = false;
            }
        });
    };

    InputFieldValidationController.prototype.getValidationByClass = function(input_value, classes){
        var validator = '',
            is_dot = input_value.indexOf(".") > -1;

        for(var i in classes){
            if(classes[i] === 'numbers-only'){
                validator = "[0-9]";

                if( (classes.indexOf("decimal") !== -1) && !is_dot ){
                    validator += "|\\.";
                }
            }
        }

        return new RegExp(validator);
    };

    exports.InputFieldValidationController = InputFieldValidationController;

}(OpenEyes.UI));
$(document).ready(function(){
    new OpenEyes.UI.InputFieldValidationController();
});
