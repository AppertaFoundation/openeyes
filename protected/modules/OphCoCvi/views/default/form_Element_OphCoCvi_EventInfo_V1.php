<?php
/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
$currentFirm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
$current_firm_subspecialty_id = $currentFirm->serviceSubspecialtyAssignment->subspecialty_id;
?>

<div class="element-fields full-width">
    <table class="cols-full last-left">
        <colgroup>
            <col class="cols-6">
            <col class="cols-6">
        </colgroup>
        <tbody>
        <tr class="col-gap">
            <td>
                <?= CHtml::activeDropDownList($element, 'site_id', Site::model()->getListForCurrentInstitution(), ['class' => 'cols-full', 'empty' => '- Please Select -']); ?>
            </td>
        </tr>
        <tr class="col-gap">
            <td>
                <?php echo $form->dropDownList($element, 'consultant_in_charge_of_this_cvi_id', Firm::model()->getListWithSpecialties(false, $current_firm_subspecialty_id), array('empty' => '- Please Select -', 'style' => 'margin-left:8px'), false, array('label' => 4, 'field' => 8)) ?>
            </td>

        </tr>

        </tbody>
    </table>


</div>

<div class="element-fields" style="display:none">
    <div class="large-6 column">
        <div class="fields-row">
            <?php echo $form->dropDownList($element, 'site_id', Site::model()->getListForCurrentInstitution(), array('empty' => '- Please Select -', 'style' => 'margin-left:8px'), false, array('label' => 4, 'field' => 8)) ?>
        </div>
    </div>
    <div class="large-6 column">
        <div class="fields-row">
            <?php echo $form->dropDownList($element, 'consultant_in_charge_of_this_cvi_id', Firm::model()->getListWithSpecialties(false, $current_firm_subspecialty_id), array('empty' => '- Please Select -', 'style' => 'margin-left:8px'), false, array('label' => 4, 'field' => 8)) ?>
        </div>
    </div>
</div>

