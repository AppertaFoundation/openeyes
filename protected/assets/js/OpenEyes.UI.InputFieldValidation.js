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

            let classList = $(this).attr('class').split(/\s+/);
            let validator = controller.getValidationByClass($(this).val(), classList);

            if(validator && !validator.test(e.key)){
                e.preventDefault();
                e.returnValue = false;
            }
        });
    };

    InputFieldValidationController.prototype.getValidationByClass = function(input_value, classes){
        let validator = '',
            is_dot = input_value.indexOf(".") > -1;

        for(let i in classes){
            if(classes[i] === 'numbers-only'){
                validator = "[0-9]";

                if( (classes.indexOf("decimal") !== -1) && !is_dot ){
                    validator += "|\\.";
                }
            }
            if(classes[i] === 'date'){
                validator = /^(?:(?:31(\/|-|\.|\s)(?:0?[13578]|1[02]|(?:Jan|Mar|May|Jul|Aug|Oct|Dec)))\1|(?:(?:29|30)(\/|-|\.|\s)(?:0?[1,3-9]|1[0-2]|(?:Jan|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec))\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(\/|-|\.|\s)(?:0?2|(?:Feb))\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(\/|-|\.|\s)(?:(?:0?[1-9]|(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep))|(?:1[0-2]|(?:Oct|Nov|Dec)))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/;
            }
        }

        return new RegExp(validator);
    };

    InputFieldValidationController.prototype.validate = function (input_value, validation_class) {
        let validator = this.getValidationByClass(input_value, validation_class);
        return validator.test(input_value);
    };

    exports.InputFieldValidationController = InputFieldValidationController;

}(OpenEyes.UI));
$(document).ready(function(){
    input_validator = new OpenEyes.UI.InputFieldValidationController();
});
