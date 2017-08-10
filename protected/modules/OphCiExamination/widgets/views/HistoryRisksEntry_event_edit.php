<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 * version. OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details. You should have received a copy of the GNU General Public License along with OpenEyes in a file titled
 * COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

?>

<?php

if (!isset($values)) {
    $values = array(
        'id' => $entry->id,
        'risk_id' => $entry->risk_id,
        'risk_display' => $entry->displayrisk,
        'has_risk' => $entry->has_risk,
        'other' => $entry->other,
        'comments' => $entry->comments,
    );
}

?>
<tr>
    <td>
        <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?=$values['id'] ?>" />
        <?php
        if ($removable) {
            $risks_opts = array(
                'options' => array(),
                'empty' => '- select -',
            );
            foreach ($risks as $risk) {
                $risks_opts['options'][$risk->id] = array('data-other' => $risk->isOther() ? '1' : '0');
            }
            echo CHtml::dropDownList($field_prefix . '[risk_id]', $values['risk_id'], CHtml::listData($risks, 'id', 'name'), $risks_opts);
        } else {
            echo CHtml::hiddenField($field_prefix . '[risk_id]', $values['risk_id']);
            echo $values['risk_display'];
        }
        ?>
        <input type="hidden" name="<?= $field_prefix ?>[other]" />
    </td>
    <td>
        <label class="inline highlight">
            <?php echo CHtml::radioButton($field_prefix . '[has_risk]', $values['has_risk'] === null || $values['has_risk'] === '', array('value' => '')); ?>
            Not checked
        </label>
        <label class="inline highlight">
            <?php echo CHtml::radioButton($field_prefix . '[has_risk]', $values['has_risk'] === '1', array('value' => '1')); ?>
            yes
        </label>
        <label class="inline highlight">
            <?php echo CHtml::radioButton($field_prefix . '[has_risk]', $values['has_risk'] === '0', array('value' => '0')); ?>
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

