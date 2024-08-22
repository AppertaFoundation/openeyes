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
$currentFirm = \Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
$current_firm_subspecialty_id = $currentFirm->serviceSubspecialtyAssignment->subspecialty_id;
$firms = \Firm::model()->getListWithSpecialties(Institution::model()->getCurrent()->id, false, $current_firm_subspecialty_id, true);
?>

<div class="element-fields full-width">
    <div class="flex-layout flex-top col-gap">
        <div class="cols-6">
            <table class="cols-full last-left">
                <colgroup>
                    <col class="cols-5">
                    <col class="cols-7">
                </colgroup>
                <tbody>
                <tr>
                    <td><?=$element->getAttributeLabel('site_id');?></td>
                    <td>
                        <?= CHtml::activeDropDownList($element, 'site_id', \Site::model()->getListForCurrentInstitution(), ['class' => 'cols-full', 'empty' => '- Please Select -']); ?>
                    </td>
                </tr>
                <tr>
                    <td><?=$element->getAttributeLabel('consultant_in_charge_of_this_cvi_id');?></td>
                    <td>
                        <?= \CHtml::activeDropDownList($element, 'consultant_in_charge_of_this_cvi_id', $firms, ['class' => 'cols-full', 'empty' => '- Please Select -']); ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <?php /* Having the following div makes sure that Event Info and Demographics elements' forms aligned properly*/ ?>
        <div class="cols-6"></div>
    </div>
</div>


