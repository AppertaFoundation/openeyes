<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>

<?php
if (!isset($values)) {
    $values = array(
        'id' => $diagnosis->id,
        'disorder_id' => $diagnosis->disorder_id,
        'disorder_display' => $diagnosis->disorder ? $diagnosis->disorder->term : '',
        'side_id' => $diagnosis->side_id,
        'side_display' => $diagnosis->side ? $diagnosis->side->adjective : 'None',
        'date' => $diagnosis->date,
        'date_display' => $diagnosis->getDisplayDate()
    );
}

?>
<tr>
    <td>
        <input type="hidden" name="<?= $model_name ?>[id][]" value="<?=$values['id'] ?>" />
        <input type="hidden" name="<?= $model_name ?>[disorder_id][]" value="<?=$values['disorder_id'] ?>" />
        <?= $values['disorder_display'] ?>
    </td>
    <td>
        <input type="hidden" name="<?= $model_name ?>[side_id][]" value="<?=$values['side_id'] ?>" />
        <?= $values['side_display'] ?>
    </td>
    <td>
        <input type="hidden" name="<?= $model_name ?>[date][]" value="<?=$values['date'] ?>" />
        <?= $values['date_display'] ?>
    </td>
    <td>
        <button class="button small warning remove">remove</button>
    </td>
</tr>
