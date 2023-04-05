<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

use OEModule\OphCiExamination\models\SystemicDiagnoses_Diagnosis;

?>

<?php
if (!isset($values)) {
    $values = array(
        'id' => $diagnosis->id,
        'disorder_id' => $diagnosis->disorder_id,
        'disorder_display' => $diagnosis->disorder ? $diagnosis->disorder->term : '',
        'is_diabetes' => isset($diagnosis->disorder) ? (strpos(strtolower($diagnosis->disorder->term), 'diabetes')) !== false : false,
        'has_disorder' => (int)$diagnosis->has_disorder,
        'side_id' => $diagnosis->side_id,
        'side_display' => $diagnosis->side ? $diagnosis->side->adjective : 'N/A',
        'date' => $diagnosis->date,
        'date_display' => $diagnosis->getDisplayDate(),
    );
}

if (!isset($values['date']) || !strtotime($values['date'])) {
    $values['date'] = null; // default to the year displayed in the select dropdowns
}

$is_new_record = isset($diagnosis) && $diagnosis->isNewRecord ? true : false;

$mandatory = !$removable;
?>

<tr data-key="<?= $row_count; ?>" data-test="systemic-diagnoses-entry">
    <td data-test="systemic-diagnoses-entry-disorder-term">
        <?= $values['disorder_display']; ?>
        <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?= $values['id'] ?>"/>
        <input type="hidden" name="<?= $field_prefix ?>[disorder_id]" value="<?= $values['disorder_id'] ?>"/>
        <input type="hidden" name="diabetic_diagnoses[]"
               value="<?= isset($values['is_diabetes']) ? $values['is_diabetes'] : 'false' ?>"/>
    </td>

    <td id="<?= "{$model_name}_{$row_count}_checked_status" ?>" data-test="systemic-diagnoses-entry-checked-status">
        <?php
        if ($removable) {
            if ($values['has_disorder'] === SystemicDiagnoses_Diagnosis::$NOT_PRESENT) { ?>
                <label class="inline cols-3">
                    <strong>Not present</strong>
                </label>
                <?= CHtml::hiddenField($field_prefix . '[has_disorder]', SystemicDiagnoses_Diagnosis::$NOT_PRESENT); ?>
            <?php } else { ?>
                <label class="inline cols-3">
                    <strong>Present</strong>
                </label>
                <?= CHtml::hiddenField($field_prefix . '[has_disorder]', SystemicDiagnoses_Diagnosis::$PRESENT); ?>
            <?php }
        } else {
            ?>
            <label class="inline highlight">
                <?php echo \CHtml::radioButton(
                    $field_prefix . '[has_disorder]',
                    $posted_not_checked,
                    array('value' => SystemicDiagnoses_Diagnosis::$NOT_CHECKED)
                ); ?>
                Not checked
            </label>
            <label class="inline highlight">
                <?php echo \CHtml::radioButton(
                    $field_prefix . '[has_disorder]',
                    $values['has_disorder'] === SystemicDiagnoses_Diagnosis::$PRESENT,
                    array('value' => SystemicDiagnoses_Diagnosis::$PRESENT)
                ); ?>
                yes
            </label>
            <label class="inline highlight">
                <?php echo \CHtml::radioButton(
                    $field_prefix . '[has_disorder]',
                    $values['has_disorder'] === SystemicDiagnoses_Diagnosis::$NOT_PRESENT,
                    array('value' => SystemicDiagnoses_Diagnosis::$NOT_PRESENT)
                ); ?>
                no
            </label>
            <?php
        }
        ?>
    </td>

    <?php $this->widget('application.widgets.EyeSelector', [
        'inputNamePrefix' => $field_prefix,
        'selectedEyeId' => $values['side_id'] ? $values['side_id'] : EyeSelector::$NOT_CHECKED
    ]); ?>

    <td data-test="systemic-diagnoses-entry-date">
        <?php
        $date_parts = date_parse_from_format('Y-m-d', $values['date']);
        if (!$date_parts['year'] && !$date_parts['month'] && !$date_parts['day']) {
            $date = '';
        } elseif ($date_parts['year'] && !$date_parts['month'] && !$date_parts['day']) {
            $date = $values['date'];
        } elseif ($date_parts['year'] && $date_parts['month'] && !$date_parts['day']) {
            $date = date('M Y', strtotime($values['date'] . '-01'));
        } else {
            $date = date(Helper::NHS_DATE_FORMAT, strtotime($values['date']));
        }
        ?>
        <input id="systemic-diagnoses-datepicker-<?= $row_count; ?>"
               data-pmu-format="d b Y"
               class="date systemic-diagnoses-date"
               data-hidden-input-selector="#systemic-diagnoses-date-<?= $row_count; ?>"
               placeholder="dd Mth YYYY"
               value="<?= $date ?>"
        >
        <input type="hidden" id="systemic-diagnoses-date-<?= $row_count; ?>" name="<?= $field_prefix ?>[date]"
               value="<?= $values['date'] ?>">

    </td>
    <td>
        <i class="js-has-tooltip oe-i info small pad right"
           data-tooltip-content="You can enter date format as dd Mth yyyy, or Mth yyyy or yyyy."></i>
    </td>
    <?php if ($removable) : ?>
        <td>
            <i class="oe-i trash"></i>
        </td>
    <?php else : ?>
        <td>read only</td>
    <?php endif; ?>
</tr>
