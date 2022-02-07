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

$(document).ready(function(){

(function (exports) {

    'use strict';

    function MultiSelectDropDownListController(options) {
        this.options = $.extend(true, {}, MultiSelectDropDownListController._defaultOptions, options);
        this.init();
    }

    MultiSelectDropDownListController._defaultOptions = {
        wrapperSelector: '.js-multiselect-dropdown-wrapper',
        listWrapperSelector: '.js-multiselect-dropdown-list-wrapper',
    };

    MultiSelectDropDownListController.prototype.init = function () {
        let controller = this;

        $('body').on('change', controller.options.wrapperSelector + ' select', function() {
            let $selected_option = $(this).find('option:selected');
            let $wrapper = $(this).closest(controller.options.wrapperSelector);
            controller.addToList($wrapper, {id:$selected_option.val(), label: $selected_option.text() });
        });

        $('body').on('click', controller.options.wrapperSelector + ' .remove-circle', function() {
            let $wrapper = $(this).closest(controller.options.wrapperSelector);
            let $li = $(this).closest('li');
            let value = $li.find('input[type="hidden"]').val();
            let $option = $('<option>', {value: value}).text($li.text());
            let $select = $wrapper.find('select');
            if (!$select.find('option[value="' + value + '"]').length && value){
                $select.append($option);
            }

            $li.remove();
            if( !$wrapper.find('li').length) {
                $wrapper.find('ul').hide();
            }
        });

    };

    MultiSelectDropDownListController.prototype.addToList = function ($wrapper, item) {
        let controller = this;
        let $li = $('<li>').text(item.label).append( $('<i>', {class: 'oe-i remove-circle small-icon pad-left'}) );
        let inputName = $wrapper.find('ul').data('inputname');
        //whitespace after li items...
        $li.after(" ");
        $li.append( $('<input>', {type:'hidden', name:inputName, value:item.id}) );
        $wrapper.find('ul').append($li);
        controller.removeFromDropDown($wrapper, item.id);
        $wrapper.find('ul').show();
    };

    MultiSelectDropDownListController.prototype.removeFromDropDown = function ($wrapper, id) {
        $wrapper.find('option[value="' + id + '"]').remove();
    };

    exports.MultiSelectDropDownListController = MultiSelectDropDownListController;

}(OpenEyes.UI));

    window.dropDownController = new OpenEyes.UI.MultiSelectDropDownListController();
});