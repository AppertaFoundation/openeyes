/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

var OpenEyes = OpenEyes || {};

OpenEyes.OphTrOperationnote = OpenEyes.OphTrOperationnote || {};
OpenEyes.OphTrOperationnote.AnaestheticController = (function () {
    "use strict";

    function AnaestheticController(options) {
        this.options = $.extend(true, {}, AnaestheticController._defaultOptions, options);
        var options = this.options;

        var anaestheticDiv = document.getElementById('OphTrOperationnote_Anaesthetic');
        var isOutpatientMinorOp = false;

        if (anaestheticDiv !== null) {
            isOutpatientMinorOp = anaestheticDiv.dataset.outpatientMinorOp === "yes" || false;

            if (isOutpatientMinorOp) {
                var fieldset = $(options['typeSelector']);

                var sedation = fieldset.find('.Sedation'),
                    GA = fieldset.find('.GA');

                $(sedation).parent().hide();
                $(sedation).prop('checked', false);

                $(GA).parent().hide();
                $(GA).prop('checked', false);
            }
        }

        // When option GA is selected,
        // hide the Delivery Method options, hide 'Given By' options,
        // set delivery method to other (all other delivery options un-checked),
        // set given by to Anaesthetist

        $(this.options['typeSelector']).on('change', 'input', function(){

            var $fieldset = $(options['typeSelector']);

            var $LA = $fieldset.find('.LA'),
                $sedation = $fieldset.find('.Sedation'),
                $GA = $fieldset.find('.GA'),
                $no_anaesthetic = $fieldset.find('.NoAnaesthetic');

            // No Anaesthetic selected
            if( $(this).hasClass('NoAnaesthetic')){
                //uncheck all other Type options
                $(this).closest('div').find('input:not(".NoAnaesthetic")').prop('checked', false);

                //this cannot be unchecked by clicking at
                $(this).prop('checked', true);
            } else {
                $(this).closest('div').find(".NoAnaesthetic").prop('checked', false);
            }

            if( $(this).closest('div').find('input:checked').length === 0 ){
                $no_anaesthetic.prop('checked', true);
            }

            if( ($GA.is(':checked') || $no_anaesthetic.is(':checked') ) && $(this).closest('div').find('input:checked').length === 1 ){
                $(options['deliveryContainer']).fadeOut();
                $(options['anaestheticContainer']).fadeOut();

                if( $GA.is(':checked') ){
                    $(options['deliverySelector']).find('input').prop('checked', false);
                    $(options['deliverySelector']).find('input.Other').prop('checked', true);

                    $(options['anaestheticSelector']).find('input').prop('checked', false);
                    $(options['anaestheticSelector']).find('input.Anaesthetist').prop('checked', true);
                }

                if($no_anaesthetic.is(':checked')){
                    $(options['deliverySelector']).find('input').prop('checked', false);
                    $(options['anaestheticSelector']).find('input').prop('checked', false);

                    $.each( $(options['anaestheticSelector']).find('input') , function( key, input ) {
                        /* Default to 'Surgeon' when it is a outpatient minor operation, per OE-11107.
                         * Otherwise, default to 'Other' as was previously the case
                         */
                        if (!isOutpatientMinorOp && $.trim($(input).parent().text()) === 'Other') {
                            $(input).prop('checked', true);
                        } else if (isOutpatientMinorOp && $.trim($(input).parent().text()) === 'Surgeon') {
                            $(input).prop('checked', true);
                        } else {
                            $(input).prop('checked', false);
                        }
                    });
                }

            } else {
                if( $LA.is(':checked') ){
                    if (isOutpatientMinorOp && $(options['anaestheticSelector']).find('input:checked').length === 0) {
                        $.each( $(options['anaestheticSelector']).find('input') , function( key, input ) {
                            // Default to 'Surgeon' when it is a outpatient minor operation, per OE-11107.
                            if ($.trim($(input).parent().text()) === 'Surgeon') {
                                $(input).prop('checked', true);
                            } else {
                                $(input).prop('checked', false);
                            }
                        });
                    }

                    $(options['deliveryContainer']).fadeIn();
                } else {
                    $(options['deliveryContainer']).fadeOut();
                }
                $(options['anaestheticContainer']).fadeIn();
            }
        });
    }

    AnaestheticController._defaultOptions = {
        sectionSelector: '.Element_OphTrOperationnote_Anaesthetic',
        typeSelector: '#Element_OphTrOperationnote_Anaesthetic_AnaestheticType',
        deliverySelector: "#Element_OphTrOperationnote_Anaesthetic_AnaestheticDelivery",
        deliveryContainer: "#Element_OphTrOperationnote_Anaesthetic_AnaestheticDelivery_container",
        anaestheticSelector: "#Element_OphTrOperationnote_Anaesthetic_anaesthetist_id",
        anaestheticContainer: "#Element_OphTrOperationnote_Anaesthetic_anaesthetist_id_container",
        agentSelector: "#AnaestheticAgent",
    };



    return AnaestheticController;
})();

$(document).ready(function() {
    $('#OphTrOperationnote_Anaesthetic').data('controller', new OpenEyes.OphTrOperationnote.AnaestheticController());
});
