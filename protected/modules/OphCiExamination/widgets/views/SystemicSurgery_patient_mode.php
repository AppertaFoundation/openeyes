<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<table>
    <colgroup>
        <col class="cols-8"><col>
    </colgroup>
    <tbody>
    <?php if ((!$operations || sizeof($operations) == 0) && !$element->no_systemicsurgery_date ) { ?>
        <div class="nil-recorded">Nil recorded</div>
    <?php } elseif ($element->no_systemicsurgery_date) { ?>
        <div class="nil-recorded">Patient has had no previous systemic surgery</div>
    <?php } else {
        foreach ($operations as $operation) { ?>
            <tr>
                <td><?= array_key_exists('object', $operation) ? $operation['object']->operation : $operation['operation']; ?></td>
                <td>
                    <?php $side = array_key_exists('side', $operation) ? $operation['side'] : (array_key_exists('object', $operation) ? $operation['object']->side : ''); ?>
                    <?php $this->widget('EyeLateralityWidget', ['laterality' => $side]) ?>
                </td>
                <td></td>
                <td>
                    <span class="oe-date">
                        <?= array_key_exists('object', $operation) ? $operation['object']->getDisplayDate() : Helper::formatFuzzyDate($operation['date']); ?>
                    </span>
                </td>
            </tr>
        <?php }
    } ?>
    </tbody>
</table>