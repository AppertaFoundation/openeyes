/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

var OpenEyes = OpenEyes || {};

OpenEyes.UI = OpenEyes.UI || {};

(function(exports) {

    'use strict';

    function EyeSelector(options) {
        this.options = $.extend(true, {}, EyeSelector._defaultOptions, options);

        this.$element = this.options.element;
        this.$table = this.$element.find('.element-fields table');
        this.leftEyeClass = this.options.leftEyeClass;
        this.rightEyeClass = this.options.rightEyeClass;
        this.noEyeClass = this.options.noEyeClass;

        this.initTriggers();
    }

    EyeSelector._defaultOptions = {
        leftEyeClass: 'js-left-eye',
        rightEyeClass: 'js-right-eye',
        noEyeClass: 'js-na-eye'
    };

    EyeSelector.prototype.initTriggers = function () {
        var controller = this;

        this.$table.on('change', '.oe-eye-lat-icons input', function(){

            let $row = $(this).closest('tr');
            let $input = $(this);

            if(($input.hasClass(controller.leftEyeClass) || $input.hasClass(controller.rightEyeClass)) && $input.is(':checked')) {
                $row.find('.' + controller.noEyeClass).prop('checked', false);
            }

            if( $input.hasClass(controller.noEyeClass) && $input.is(':checked') ){
                $row.find('.' + controller.leftEyeClass).prop('checked', false);
                $row.find('.' + controller.rightEyeClass).prop('checked', false);
            }

            if (!$input.is(':checked')) {
                if($row.find('.oe-eye-lat-icons input:checkbox:checked').length === 0 && $row.find('.' + controller.noEyeClass).length) {
                    $input.prop('checked' , true);
                }
            }

        });
    };

    exports.EyeSelector = EyeSelector;

})(OpenEyes.UI);