<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

$institution_id = Yii::app()->session['selected_institution_id'];
$criteria = new CDbCriteria();
$criteria->with = array('dispense_location_institutions', 'dispense_location_institutions.dispense_location');
$criteria->compare('t.dispense_condition_id', $model->id);
$criteria->compare('t.institution_id', $institution_id);
$criteria->compare('dispense_location_institutions.institution_id', $institution_id);
$dc_institution = OphDrPrescription_DispenseCondition_Institution::model()->find($criteria);

if (!$dc_institution) {
    $dc_institution = OphDrPrescription_DispenseCondition_Institution::model();
}
?>
<div class="cols-full">
    <?=\CHtml::activeHiddenField($model, 'id') ?>
    <table class="standard cols-full">
        <colgroup>
            <col class="cols-2">
            <col class="cols-full">
        </colgroup>
        <tbody>
        <tr>
            <td>Name</td>
            <td>
                <?=\CHtml::activeTextField(
                    $model,
                    'name',
                    ['class' => 'cols-full']
                ) ?>
            </td>
        </tr>
        <tr>
            <td>Dispense Locations</td>
            <td>
                <?php $form->multiSelectList(
                    $dc_institution,
                    CHtml::modelName($dc_institution) . '[dispense_location_institutions]',
                    'dispense_location_institutions',
                    'id',
                    CHtml::listData(OphDrPrescription_DispenseLocation_Institution::model()->findAll(
                        array(
                            'condition' => 't.institution_id = :id',
                            'with' => array('dispense_location'),
                            'params' => [':id' => Yii::app()->session['selected_institution_id']],
                            'order' => 'dispense_location.display_order',
                        )
                    ), 'id', 'dispense_location.name'),
                    null,
                    array('empty' => '- Add -', 'label' => 'Locations', 'nowrapper' => true, 'class' => 'cols-full')
                ) ?>
            </td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="2">
                <?=\CHtml::submitButton(
                    'Save',
                    [
                        'class' => 'button large primary event-action',
                        'name' => 'save',
                        'id' => 'et_save'
                    ]
                ) ?>
                <?=\CHtml::submitButton(
                    'Cancel',
                    [
                        'data-uri' => '/OphDrPrescription/admin/DispenseCondition/index',
                        'class' => 'warning button large primary event-action',
                        'name' => 'cancel',
                        'id' => 'et_cancel',
                    ]
                ) ?>
            </td>
        </tr>
        </tfoot>
    </table>
</div>
