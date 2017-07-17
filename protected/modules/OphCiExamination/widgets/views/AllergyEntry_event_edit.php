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
        'allergy_id' => $entry->allergy_id,
        'allergy_display' => $entry->displayallergy,
        'other' => $entry->other,
        'comments' => $entry->comments,
    );
}

?>
<tr>
    <td>
        <input type="hidden" name="<?= $model_name ?>[id][]" value="<?=$values['id'] ?>" />
        <input type="hidden" name="<?= $model_name ?>[allergy_id][]" value="<?=$values['allergy_id'] ?>" />
        <input type="hidden" name="<?= $model_name ?>[other][]" value="<?=$values['other'] ?>" />
        <?= $values['allergy_display'] ?>
    </td>
    <td>
        <input type="hidden" name="<?= $model_name ?>[comments][]" value="<?=$values['comments'] ?>" />
        <?= $values['comments'] ?>
    </td>
    <td class="edit-column" <?php if (!$editable) {?>style="display: none;"<?php } ?>>
        <button class="button small warning remove">remove</button>
    </td>
</tr>
