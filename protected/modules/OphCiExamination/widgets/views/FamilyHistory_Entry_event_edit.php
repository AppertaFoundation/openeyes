<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

?>

<?php
if (!isset($values)) {
    $values = array(
        'id' => $entry->id,
        'relative_id' => $entry->relative_id,
        'relative_display' => $entry->displayrelative,
        'other_relative' => $entry->other_relative,
        'side_id' => $entry->side_id,
        'side_display' => $entry->side,
        'condition_id' => $entry->condition_id,
        'condition_display' => $entry->displaycondition,
        'other_condition' => $entry->other_condition,
        'comments' => $entry->comments,
    );
}

?>
<tr>
    <td>
        <input type="hidden" name="<?= $model_name ?>[id][]" value="<?=$values['id'] ?>" />
        <input type="hidden" name="<?= $model_name ?>[relative_id][]" value="<?=$values['relative_id'] ?>" />
        <input type="hidden" name="<?= $model_name ?>[other_relative][]" value="<?=$values['other_relative'] ?>" />
        <?= $values['relative_display'] ?>
    </td>
    <td>
        <input type="hidden" name="<?= $model_name ?>[side_id][]" value="<?=$values['side_id'] ?>" />
        <?= $values['side_display'] ?>
    </td>
    <td>
        <input type="hidden" name="<?= $model_name ?>[condition_id][]" value="<?=$values['condition_id'] ?>" />
        <input type="hidden" name="<?= $model_name ?>[other_condition][]" value="<?=$values['other_condition'] ?>" />
        <?= $values['condition_display'] ?>
    </td>
    <td>
        <input type="hidden" name="<?= $model_name ?>[comments][]" value="<?=$values['comments'] ?>" />
        <?= $values['comments'] ?>
    </td>
    <td class="edit-column" <?php if (!$editable) {?>style="display: none;"<?php } ?>>
        <button class="button small warning remove">remove</button>
    </td>
</tr>
