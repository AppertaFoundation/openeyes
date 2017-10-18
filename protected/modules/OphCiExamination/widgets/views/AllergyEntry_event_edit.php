<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>

<?php
if (!isset($values)) {
    $values = array(
        'id' => $entry->id,
        'allergy_id' => $entry->allergy_id,
        'allergy_display' => $entry->displayallergy,
        'other' => $entry->other,
        'comments' => $entry->comments,
    );
}
?>

<tr class="row-<?=$row_count;?><?php if($removable){ echo " read-only"; } ?>" data-key="<?=$row_count;?>">
    <td>
        <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?=$values['id'] ?>" />
        <input type="hidden" name="<?= $field_prefix ?>[other]" value="<?=$values['other'] ?>" />

        <?php if ($removable): ?>
        <?php
            $allergies_opts = array(
                'options' => array(),
                'empty' => '- select -',
                'class' => 'other'
            );

            foreach ($allergies as $allergy) {
                $allergies_opts['options'][$allergy->id] = array(
                        'data-other' => $allergy->isOther() ? '1' : '0',
                );
            }

            echo CHtml::dropDownList($field_prefix . "[allergy_id]", $values['allergy_id'], CHtml::listData($allergies, 'id', 'name'), $allergies_opts);
            $show_other = $values['allergy_id'] && array_key_exists($values['allergy_id'], $allergies_opts['options']) && ($allergies_opts['options'][$values['allergy_id']]['data-other'] === '1');
        ?>
        <span class="<?=  $show_other ? : 'hidden'?> <?= $model_name ?>_other_wrapper">
            <?php echo CHtml::textField($field_prefix . '[other]', $values['other'], array('class' => 'other-type-input', 'autocomplete' => Yii::app()->params['html_autocomplete']))?>
        </span>
        <?php else: ?>
            <?=$values['allergy_display']; ?>
            <input type="hidden" name="<?= $field_prefix ?>[allergy_id]" value="<?=$values['allergy_id'] ?>" />
        <?php endif; ?>
    </td>
    <td>
        <?php if ($removable): ?>
            <?php echo CHtml::textField($field_prefix . '[comments]', $values['comments'], array('autocomplete' => Yii::app()->params['html_autocomplete']))?>
        <?php else: ?>
            <input type="hidden" name="<?= $field_prefix ?>[comments]" value="<?=$values['comments'] ?>" />
            <?= $values['comments'] ?>
        <?php endif; ?>

    </td>

    <td class="edit-column">
        <?php if($removable) : ?>
            <button class="button small warning remove">remove</button>
            <?php else: ?>
            read only
        <?php endif; ?>
    </td>
</tr>