<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 * version. OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details. You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled
 * COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OEModule\OphCiExamination\models\HistoryRisksEntry;

?>

<?php

if (!isset($values)) {
    $values = array(
        'id' => $entry->id,
        'risk_id' => $entry->risk_id,
        'risk_display' => $entry->displayrisk,
        'has_risk' => $entry->has_risk,
        'other' => $entry->other,
        'comments' => $entry->comments
    );
}
?>
<tr data-key="<?=$row_count?>">
    <td>
        <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?=$values['id'] ?>" />
        <?php
        if ($removable) {
            $risks_opts = array(
                'options' => array(),
                'empty' => '- select -',
                'class' => $model_name . '_risk_id'
            );
            foreach ($risks as $risk) {
                $risks_opts['options'][$risk->id] = array('data-other' => $risk->isOther() ? '1' : '0');
            }
            echo CHtml::dropDownList($field_prefix . '[risk_id]', $values['risk_id'], CHtml::listData($risks, 'id', 'name'), $risks_opts);
            $show_other = $values['risk_id'] && array_key_exists($values['risk_id'], $risks_opts['options']) && ($risks_opts['options'][$values['risk_id']]['data-other'] === '1');
        ?>

          <span class="<?=  $show_other ? : 'hidden'?> <?= $model_name ?>_other_wrapper">
            <?php echo CHtml::textField($field_prefix . '[other]', $values['other'], array('class' => 'other-type-input', 'autocomplete' => Yii::app()->params['html_autocomplete']))?>
          </span>
        <?php
        } else {
            echo CHtml::hiddenField($field_prefix . '[risk_id]', $values['risk_id']);
            echo CHtml::hiddenField($field_prefix . '[other]', $values['other']);
            echo $values['risk_display'];
        }
        ?>

    </td>
    <td id="OEModule_OphCiExamination_models_HistoryRisks_entries_<?=$row_count?>_risk_id_error">
        <label class="inline highlight">
            <?php echo CHtml::radioButton($field_prefix . '[has_risk]', $posted_not_checked, array('value' => HistoryRisksEntry::$NOT_CHECKED)); ?>
            Not checked
        </label>
        <label class="inline highlight">
            <?php echo CHtml::radioButton($field_prefix . '[has_risk]', $values['has_risk'] === (string) HistoryRisksEntry::$PRESENT, array('value' => HistoryRisksEntry::$PRESENT)); ?>
            yes
        </label>
        <label class="inline highlight">
            <?php echo CHtml::radioButton($field_prefix . '[has_risk]', $values['has_risk'] === (string) HistoryRisksEntry::$NOT_PRESENT, array('value' => HistoryRisksEntry::$NOT_PRESENT)); ?>
            no
        </label>
    </td>
    <td>
        <input type="text" name="<?= $field_prefix ?>[comments]" value="<?=$values['comments'] ?>" />
    </td>
    <td class="edit-column">
        <button class="button small warning remove" <?php if (!$removable) {?>style="display: none;"<?php } ?>>remove</button>
    </td>
</tr>
