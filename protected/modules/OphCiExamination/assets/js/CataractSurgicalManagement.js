/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};

(function(exports) {

    /**
     *
     * @param options
     * @constructor
     */
    function CataractSurgicalManagementController(options) {
        this.options = $.extend(true, {}, CataractSurgicalManagementController._defaultOptions, options);
        this.$element = this.options.element;
        this.$eyeRadioButtons = this.$element.find(this.options.radioButtonSelector);

        this.initialiseEyeSelectors();
        this.initialiseAdderDialog('left');
        this.initialiseAdderDialog('right');
    }

    /**
     * Data structure containing all the configuration options for the controller
     * @private
     */
    CataractSurgicalManagementController._defaultOptions = {
        element: undefined,
        addButtonSelectorTemplate: '.js-csm-{side}-add-btn',
        radioButtonSelector: '.js-csm-eye-radio',
    };

    CataractSurgicalManagementController.prototype.initialiseEyeSelectors = function () {
        this.$eyeRadioButtons.click( e => {
            let $clickedButton = $(e.target);
            let clickedSide = $clickedButton.data('side');
            let clickedOrder = $clickedButton.val();
            this.$eyeRadioButtons.each( (index, button) => {
                let $button = $(button);
                if ($button.data('side') !== clickedSide) {
                    if ($button.val() === clickedOrder) {
                        $button.prop('checked', false);
                    } else {
                        $button.prop('checked', true);
                    }
                }
            });
        });
    };

    CataractSurgicalManagementController.prototype.validateSelections = function (processedSelections) {
        if (processedSelections['discussed'].id === 0) {
            return [true, {}];
        }
        switch(processedSelections['refractive_category'].id) {
            case 0:
                return [true, {}];
            case 1:
                if (!('refractive_myopia' in processedSelections)) {
                    return [false, {'error': 'You must select a refractive target'}];
                }
                return [true, {}];
            case 2:
                if (!('primary_reason' in processedSelections)) {
                    return [false, {'error': 'You must select a primary reason for surgery.'}];
                }
                if (!('first_digit' in processedSelections === 'second_digit' in processedSelections &&
                      'second_digit' in processedSelections === 'decimal' in processedSelections)) {
                    return [false, {'error': 'Error in refractive target, all values must be selected or none.'}];
                }
                if (!('sign' in processedSelections) && 'first_digit' in processedSelections) {
                    if (!(processedSelections['first_digit'] === 0 && processedSelections['second_digit'] === 0 && processedSelections['decimal'] === '.00')) {
                        return [false, {'error': 'Error in refractive target, target must be 0.00 if no sign is selected.'}];
                    }
                }
                let params = {
                    'hasSign' : 'sign' in processedSelections,
                    'hasTarget' : 'first_digit' in processedSelections && 'second_digit' in processedSelections && 'decimal' in processedSelections,
                };
                return [true, params];
        }
    }

    CataractSurgicalManagementController.prototype.processSelections = function (selectedItems, selectedAdditions, side) {
        let allSelectedItems = selectedItems.concat(selectedAdditions);
        let processedSelections = {};
        for (selectedItem of allSelectedItems) {
            switch(selectedItem['type']) {
                case 'first_digit':
                case 'second_digit':
                    processedSelections[selectedItem['type']] = selectedItem[side+'_refractive_group_other'];
                    break;
                case 'sign':
                case 'decimal':
                    processedSelections[selectedItem['type']] = selectedItem['addition'];
                    break;
                case 'refractive_emmetropia':
                case 'refractive_myopia':
                    processedSelections[selectedItem['type']] = selectedItem['value'];
                    break;
                case 'primary_reason':
                case 'guarded_prognosis':
                case 'discussed':
                case 'refractive_category':
                    processedSelections[selectedItem['type']] = {'id': selectedItem['id'], 'label': selectedItem['label']};
                    break;
                default:
                    // Internal error, cannot be caused by end user, only by code change.
                    console.log('ERROR: Unsupported itemset added');
            }
        }
        return processedSelections;
    }

    CataractSurgicalManagementController.prototype.initialiseAdderDialog = function (side) {
        new OpenEyes.UI.AdderDialog({
            id: 'add-csm-value-' + side,
            deselectOnReturn: true,
            openButton: $(this.options.addButtonSelectorTemplate.replace('{side}',side)),
            itemSets: [
                new OpenEyes.UI.AdderDialog.ItemSet(this.options[side+ 'PrimaryReasons'], this.options.primaryReasonsOptions),
                new OpenEyes.UI.AdderDialog.ItemSet(this.options[side+ 'GuardedPrognosis'], this.options.guardedPrognosisOptions),
                new OpenEyes.UI.AdderDialog.ItemSet(this.options[side + 'Discussed'], this.options[side + 'DiscussedOptions']),
                new OpenEyes.UI.AdderDialog.ItemSet(this.options[side + 'RefractiveCategories'], this.options[side + 'RefractiveCategoriesOptions']),
                new OpenEyes.UI.AdderDialog.ItemSet(this.options[side + 'RefractiveEmmetropia'], this.options[side + 'RefractiveEmmetropiaOptions']),
                new OpenEyes.UI.AdderDialog.ItemSet(this.options[side + 'RefractiveMyopia'], this.options[side + 'RefractiveMyopiaOptions']),
                new OpenEyes.UI.AdderDialog.ItemSet([], this.options[side + 'RefractiveTargetOptions']),
            ],
            onReturn: (adderDialog, selectedItems, selectedAdditions) => {
                let processedSelections = this.processSelections(selectedItems, selectedAdditions, side);
                let [selectionsAreValid, selectionParams] = this.validateSelections(processedSelections);
                if ('error' in selectionParams) {
                    new OpenEyes.UI.Dialog.Alert({
                        content: selectionParams['error'],
                    }).open();
                    return;
                }

                let primaryReason = processedSelections['primary_reason'];
                let guardedPrognosis = processedSelections['guarded_prognosis'];

                let discussedLabel = '<span class="none">Not recorded</span>';
                let discussedId = '';
                if ('discussed' in processedSelections) {
                    discussedLabel = processedSelections['discussed'].id === 1 ? '' : 'Refractive target not discussed';
                    discussedId = processedSelections['discussed'].id;
                }

                let refractiveTarget = '<span class="none">Not recorded</span>';
                let refractiveTargetValue = '';
                if (discussedId === 1) {
                    switch(processedSelections['refractive_category'].id) {
                        case 0:
                            refractiveTargetValue = '0.00';
                            refractiveTarget = refractiveTargetValue + 'D';
                            break;
                        case 1:
                            refractiveTargetValue = processedSelections['refractive_myopia'];
                            refractiveTarget = refractiveTargetValue + 'D';
                            break;
                        case 2:
                            if (selectionParams['hasTarget']) {
                                refractiveTargetValue = (selectionParams['hasSign'] ? processedSelections['sign'] : '') +
                                    String(processedSelections['first_digit']) +
                                    String(processedSelections['second_digit']) +
                                    processedSelections['decimal'];
                                refractiveTarget = refractiveTargetValue + 'D';
                            }
                            break;
                    }
                }

                let refractive_category = 'refractive_category' in processedSelections ?
                    processedSelections['refractive_category'].id :
                    '';

                // set visible form elements
                $('#'+side+'_reason_entry').html(primaryReason.label);
                $('#'+side+'_guarded_prognosis_entry').html(guardedPrognosis.label === 'No' ? 'No guarded prognosis' : 'Guarded prognosis');
                $('#'+side+'_refraction_entry').html(refractiveTarget);
                $('#'+side+'_refraction_entry').toggle(discussedLabel === '');
                $('#'+side+'_refraction_entry').parents('td:first').toggle(discussedLabel === '');
                $('#'+side+'_discussed_entry').html(discussedLabel);

                // set hidden form elements
                $('#'+side+'_primary_reason_hidden').val(primaryReason.id);
                $('#'+side+'_guarded_prognosis_hidden').val(guardedPrognosis.id);
                $('#'+side+'_refraction_category_hidden').val(refractive_category);
                $('#'+side+'_refraction_hidden').val(refractiveTargetValue);
                $('#'+side+'_discussed_hidden').val(discussedId);
            },
            onOpen: () => {
                let $adderObject = $("#add-csm-value-"+side);
                let refractiveTargetLabel = $('#'+side+'_refraction_entry').html();
                if (!refractiveTargetLabel.includes('Not recorded')) {
                    let index = 0;
                    let sign = '';
                    if (['-', '+'].includes(refractiveTargetLabel.charAt(index))) {
                        sign = refractiveTargetLabel.charAt(index++);
                    }
                    let digit0 = refractiveTargetLabel.charAt(index++);
                    let digit1 = refractiveTargetLabel.charAt(index++);
                    let decimal = refractiveTargetLabel.substr(index,3);
                    $('[data-id="'+side+'_discussed"] .selected').click();
                    let refractive_category = $('#'+side+'_refraction_category_hidden').val();
                    $('[data-conditional-id="'+side+'-refractive-category-'+refractive_category+'"]').click();
                    $('[data-conditional-id="'+side+'-refractive-category-'+refractive_category+'"]').addClass('selected');
                    switch(refractive_category) {
                        case '0':
                            $adderObject.find("[data-type='refractive_emmetropia']").addClass('selected');
                            break;
                        case '1':
                            let value = refractiveTargetLabel.replace('D','');
                            let $refractiveValue =  $adderObject.find("[data-type='refractive_myopia'][data-value='"+value+"']");
                            if(!$refractiveValue.length) {
                                $refractiveValue =   $adderObject.find("[data-type='refractive_myopia'][data-value='"+sign+digit1+decimal+"']");
                            }
                            $refractiveValue.addClass('selected');
                            break;
                        case '2':
                            $adderObject.find("[data-addition='"+sign+"']").addClass('selected');
                            $adderObject.find('#number-digit-0').find("[data-"+side+"_refractive_group_other='"+digit0+"']").addClass('selected');
                            $adderObject.find('#number-digit-1').find("[data-"+side+"_refractive_group_other='"+digit1+"']").addClass('selected');
                            $adderObject.find("[data-addition='"+decimal+"']").addClass('selected');
                            break;
                    }
                }
            },
        });
    };

    exports.CataractSurgicalManagementController = CataractSurgicalManagementController;
})(OpenEyes.OphCiExamination);

