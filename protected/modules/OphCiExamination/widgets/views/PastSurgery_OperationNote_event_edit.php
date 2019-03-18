<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php
if (!isset($values)) {
    $values = [
        'operation' => $op,
        'side' => $side,
        'date' => $date,
    ];
} ?>
<tr class="row-<?= $row_count; ?>read-only" id="<?= $model_name ?>_operations_<?= $row_count ?>">
    <td>
        <?= $values['operation'] ?>
    </td>
    <td class="past-surgery-entry has-operation"></td>
    <td>
        <?php
        foreach (['Right', 'Left'] as $eye_side) {
            if ($values['side'] === $eye_side || $values['side'] === 'Both') { ?>
                <i class="oe-i laterality <?= $values['side'][0]?> small pad"></i>
            <?php } else { ?>
                <i class="oe-i laterality small pad NA"></i>
            <?php }
        } ?>
    </td>
    <td></td>
    <td>
        <?= Helper::formatFuzzyDate($values['date']) ?>
    </td>
    <td>read only
        <i class="js-has-tooltip oe-i info small pad right"
           data-tooltip-content="This operation is recorded as an Operation Note event in OpenEyes and cannot be edited here"></i>
    </td>
</tr>