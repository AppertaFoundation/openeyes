/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

var OpenEyes = OpenEyes || {};

OpenEyes.OphTrOperationnote = OpenEyes.OphTrOperationnote || {};
OpenEyes.OphTrOperationnote.AnaestheticController = (function () {
    "use strict";

    function AnaestheticController(options) {
        this.options = $.extend(true, {}, AnaestheticController._defaultOptions, options);
        var options = this.options;

        // When option GA is selected,
        // hide the Delivery Method options, hide 'Given By' options,
        // set delivery method to other (all other delivery options un-checked),
        // set given by to Anaesthetist

        $(this.options['sectionSelector']).on('change', 'input', function(){
            if( $.trim($(this).parent().text()) === 'GA' && $(this).is(':checked')){
                $(options['deliverySelector']).hide();
                $(options['anaestheticSelector']).hide();

                $.each( $(options['deliverySelector']).find('input') , function( key, input ) {
                    if( $.trim($(input).parent().text()) === 'Other'){
                        $(input).prop('checked', true);
                    } else {
                        $(input).prop('checked', false);
                    }
                });

                $.each( $(options['anaestheticSelector']).find('input') , function( key, input ) {
                    if( $.trim($(input).parent().text()) === 'Anaesthetist'){
                        $(input).prop('checked', true);
                    } else {
                        $(input).prop('checked', false);
                    }
                });
            } else if( $.trim($(this).parent().text()) === 'GA' && !$(this).is(':checked')){
                $(options['deliverySelector']).show();
                $(options['anaestheticSelector']).show();
            };
        });
    }

    AnaestheticController._defaultOptions = {
        sectionSelector: '.Element_OphTrOperationnote_Anaesthetic',
        typeSelector: '#Element_OphTrOperationnote_Anaesthetic_AnaestheticType',
        deliverySelector: "#Element_OphTrOperationnote_Anaesthetic_AnaestheticDelivery",
        anaestheticSelector: "#Element_OphTrOperationnote_Anaesthetic_anaesthetist_id",
        agentSelector: "#AnaestheticAgent",
    };



    return AnaestheticController;
})();

$(document).ready(function() {
    $('#OphTrOperationnote_Anaesthetic').data('controller', new OpenEyes.OphTrOperationnote.AnaestheticController());
});