$(".foster_images_dialog").dialog({
    autoOpen: false,
    modal: true,
    resizable: false,
    width: 480
});

$(this).delegate('a.foster_images_link', 'click', function(e) {
    var side = $(this).closest('[data-side]').attr('data-side');
    $('.foster_images_dialog[data-side="'+side+'"]').dialog('open');
    e.preventDefault();
});
$('body').delegate('.foster_images_dialog area', 'click', function() {
    var value = $(this).attr('data-vh');
    var side = $(this).closest('[data-side]').attr('data-side');
    $('.foster_images_dialog[data-side="'+side+'"]').dialog('close');
    $('#OEModule_OphCiExamination_models_Element_OphCiExamination_'+side+'_van_herick_id option').attr('selected', function () {
        return ($(this).text() == value + '%');
    });
});

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
        this.modelName = this.options.modelName;
        this.$element = $('#' + this.modelName);
        this.img_link_selector = this.options.img_link_selector;

        this.initialiseTriggers();

    }

    VanHerickController._defaultOptions = {
        modelName: 'OEModule_OphCiExamination_models_VanHerick',
        dropdown: '_van_herick_id',
        img_container_class: 'foster_images_dialog',
        img_link_selector: '.foster_images_link',
    };

    VanHerickController.prototype.initialiseTriggers = function () {

        var controller = this;


        this.$element.on('click', this.img_link_selector, function(){
console.log( $(this));
            controller.initialiseDialog( $(this).closest('.element-eye').data('side') );
        });
    };

    VanHerickController.prototype.initialiseDialog = function (side) {
        var controller = this;

        console.log(".foster_images_dialog." + side);
        $(".foster_images_dialog." + side).dialog({
            autoOpen: true,
            modal: true,
            resizable: false,
            width: 480
        });
    };

    exports.VanHerickController = VanHerickController;

})(OpenEyes.OphCiExamination);
