<?php
/**
 * (C) Copyright Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * @var OphTrOperationchecklists_Observations $name_stub
 */
?>
<?php
// INR results
$api = Yii::app()->moduleAPI->get('OphInLabResults');
$patientId = $_GET['patient_id'] ?? $this->patient->id;
$inrResult = $api->getLabResultTypeResult($patientId, $this->event->id, "INR");
?>
<td></td>
<tr class="no-line">
    <td>
        <span class="data-label"><?= $element->getAttributeLabel('blood_pressure') ?>:</span>
    </td>
    <td>
        <span class="data-value"><?php echo (!empty($element->blood_pressure_systolic)) ? $element->blood_pressure_systolic . '/' . $element->blood_pressure_diastolic: '' ?><span>
    </td>
    <td>
        <span class="data-label"><?= $element->getAttributeLabel('pulse') ?>:</span>
    </td>
    <td>
        <span class="data-value"><?php echo (!empty($element->pulse)) ? $element->pulse : '' ?><span>
    </td>
    <td>
        <span class="data-label"><?= $element->getAttributeLabel('temperature') ?>:</span>
    </td>
    <td>
        <span class="data-value"><?php echo (!empty($element->temperature)) ? $element->temperature : '' ?><span>
    </td>
</tr>
<tr class="no-line">
    <td>
        <span class="data-label"><?= $element->getAttributeLabel('respiration') ?>:</span>
    </td>
    <td>
        <span class="data-value"><?php echo (!empty($element->respiration)) ? $element->respiration : '' ?></span>
    </td>
    <td>
        <span class="data-label"><?= $element->getAttributeLabel('o2_sat') ?>:</span>
    </td>
    <td>
        <span class="data-value"><?php echo (!empty($element->o2_sat)) ? $element->o2_sat : '' ?></span>
    </td>
    <td>
        <span class="data-label"><?= $element->getAttributeLabel('ews') ?>:</span>
    </td>
    <td>
        <span class="data-value"><?php echo (!empty($element->blood_glucose)) ? $element->ews : '' ?></span>
    </td>
</tr>
<tr class="no-line">
    <td>
        <span class="data-label"><?= $element->getAttributeLabel('blood_glucose') ?>:</span>
    </td>
    <td>
        <span class="data-value"><?php echo (!empty($element->blood_glucose)) ? $element->blood_glucose : '' ?></span>
    </td>
    <td>
        <span class="data-label"><?= $element->getAttributeLabel('hba1c') ?>:</span>
    </td>
    <td>
        <span class="data-value"><?php echo (!empty($element->hba1c)) ? $element->hba1c : '' ?></span>
    </td>
    <td>
        <span class="data-label">INR:</span>
    </td>
    <td>
        <span class="data-value">
            <?php if (isset($inrResult)) {
                echo $inrResult['result'];
            } else { ?>
                Not recorded
                <i class="js-has-tooltip oe-i info small pad right" data-tooltip-content="INR result is not recorded for this patient. This can be recorded in the Lab Results event."></i>
            <?php } ?>
        </span>
    </td>
</tr>
