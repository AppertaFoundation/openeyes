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

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};

(function (exports) {

    function VanHerickController(options) {
        this.options = $.extend(true, {}, VanHerickController._defaultOptions, options);
        this.$element = this.options.element;
        this.imgLinkClass = this.options.imgLinkClass;
        this.imgContainer = $('.' + this.imgLinkClass);
        this.imgContainerClass = this.options.imgContainerClass;

        this.initialise();
        this.initialiseTriggers();
    }

    VanHerickController._defaultOptions = {
        element: undefined,
        dropdown: '_van_herick_id',
        imgContainerClass: 'js-foster_images_dialog',
        imgLinkClass: 'js-foster_images_link',
    };

    VanHerickController.prototype.initialise = function () {
        // in case if the user removes then re-adds the element
        $('.' + this.imgContainerClass + '.ui-dialog-content').closest('.ui-dialog').remove();

        this.initialiseDialog();
    };

    VanHerickController.prototype.initialiseTriggers = function () {

        var controller = this;
        var $dialog = $("." + controller.imgContainerClass);

        this.$element.on('click', '.' + this.imgLinkClass, function () {
            let side = $(this).closest('.element-eye').data('side');

            $dialog.data('side', side);
            $dialog.dialog('open');
        });

        $('.' + this.imgContainerClass).on('click', 'map area', function () {
            var value = $(this).data('vh');
            var side = $dialog.data('side');
            var $dropdown = controller.$element.find('select[name*="[' + side + '_van_herick_id]"]');

            $dropdown.find('option').attr('selected', function () {
                return ($(this).text() == value + '%');
            });

            $dialog.dialog('close');

        });
    };

    VanHerickController.prototype.initialiseDialog = function () {
        var controller = this;

        //@TODO: OK, this is BAD, but didn't find any workaround - let me know if can fix it!
        // Without setTimeout the .dialog('open') won't work : cannot call methods on dialog prior to initialization; attempted to call method 'open'
        setTimeout(function(){
            $("." + controller.imgContainerClass).dialog({
                autoOpen: false,
                modal: true,
                resizable: false,
                width: 480
            })
        },50);
    };

    exports.VanHerickController = VanHerickController;

})(OpenEyes.OphCiExamination);
