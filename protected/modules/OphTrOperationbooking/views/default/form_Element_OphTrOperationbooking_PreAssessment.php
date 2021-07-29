<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>


<?php
if (!isset($this->patient->contact->mobile_phone) || $this->patient->contact->mobile_phone == '') : ?>
    <div class="alert-box alert">Patient has no mobile phone number</div>
<?php endif; ?>
<div class="element-fields full-width flex-layout">
    <table class="cols-11 last-left">
        <tbody>
        <tr>
            <td>
                <?= $element->getAttributeLabel('type_id')?>
            </td>
            <td class="js-preassessment_type_dropdown-tr">
                <?php
                echo $form->radioButtons(
                    $element,
                    'type_id',
                    CHtml::listData(
                        OphTrOperationbooking_PreAssessment_Type::model()->findAllByAttributes(array('active' => 1)),
                        'id',
                        'name'
                    ),
                    null,
                    false,
                    false,
                    false,
                    false,
                    array('nowrapper' => true, 'options' => $element->getPreassessmentTypes())
                ); ?>
            </td>
        </tr>
        <tr class="js-preassessment_location_dropdown-tr <?= !is_null($element->type) && (int)$element->type->use_location === 1 ? 'show' : 'hidden'?>">
            <td>
                <?= $element->getAttributeLabel('location_id')?>
            </td>
            <td>
                <?= CHtml::activeDropDownList(
                    $element,
                    'location_id',
                    CHtml::listData(OphTrOperationbooking_PreAssessment_Location::model()->findAllByAttributes(array('active' => 1)), 'id', 'name'),
                    array('empty' => '- None -', 'nowrapper' => true),
                ); ?>
            </td>
        </tr>
        </tbody>
    </table>
</div>
