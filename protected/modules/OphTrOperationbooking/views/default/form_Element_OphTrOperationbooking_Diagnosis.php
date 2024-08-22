<?php /**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
$event_errors = OphTrOperationbooking_BookingHelper::validateElementsForEvent($this->open_elements);
?>

<div class="element-fields flex-layout full-width">
    <table class="cols-11" id="editDiagnosis">
        <colgroup>
            <col class="cols-1">
            <col class="cols-1">
        </colgroup>
        <tbody>
        <tr>
            <td>
                <span class="oe-eye-lat-icons">
                    <?php echo $form->radioButtons(
                        $element,
                        'eye_id',
                        CHtml::listData(Eye::model()->findAll(array('order' => 'display_order asc')), 'id', 'name'),
                        null,
                        false,
                        false,
                        false,
                        '',
                        array(
                            'nowrapper' => true,
                            'label-class' => $event_errors ? 'error' : ''
                        )
                    ) ?>
                </span>
            </td>
            <td></td>
            <td class="large-text">
                <div class="panel diagnosis hide" id="enteredDiagnosisText">
                    <?= isset($element->disorder) ? $element->disorder->term : 'Please use the + button to add a listing diagnosis'?>
                </div>
                <?php $form->hiddenInput($element, 'disorder_id');?>
            </td>
            <td>
        </tr>
        </tbody>
    </table>
    <div class="add-data-actions flex-item-bottom" id="operation-booking-diagnoses-popup">
        <button class="button hint green js-add-select-search" type="button" id="add-operation-booking-diagnosis" data-test="add-diagnosis-btn">
            <i class="oe-i plus pro-theme"></i>
        </button>
    </div>
    <?php $diagnoses = CommonOphthalmicDisorder::getList(
        Firm::model()->findByPk($this->selectedFirmId),
        false,
        true,
        $this->patient
    ); ?>
    <script type="text/javascript">
        new OpenEyes.UI.AdderDialog({
            openButton: $('#add-operation-booking-diagnosis'),
            itemSets: [new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
                array_map(function ($id, $label) {
                    return ['label' => $label, 'id' => $id];
                }, array_keys($diagnoses), $diagnoses)
            ) ?>)],
            onReturn: function (adderDialog, selectedItems) {
                $('#enteredDiagnosisText').html(selectedItems[0].label);
                $('[id$="disorder_id"]').val(selectedItems[0].id);
            },
            searchOptions: {
                searchSource: '/disorder/autocomplete'
            }
        });
    </script>