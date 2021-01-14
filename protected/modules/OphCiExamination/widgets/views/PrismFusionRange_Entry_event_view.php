<?php
/**
 * (C) Apperta Foundation, 2020
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

/** @var  \OEModule\OphCiExamination\models\PrismFusionRange_Entry $entry */
/** @var \OEModule\OphCiExamination\widgets\PrismFusionRange $this */

$attributes = ['bo', 'bi', 'bu', 'bd'];
?>

<tr>
    <td><?= $entry->display_prism_over_eye ?></td>

    <?php foreach ($attributes as $attr) { ?>
        <td><?php if (!empty($entry->{"near_{$attr}"})) { ?>
            <?= CHtml::encode($entry->{"near_{$attr}"}) ?> Δ <b><?= strtoupper($attr) ?></b>
            <?php } else { ?>
            -
            <?php } ?>
        </td>
    <?php } ?>
    <?php foreach ($attributes as $attr) { ?>
        <td><?php if (!empty($entry->{"distance_{$attr}"})) { ?>
                <?= CHtml::encode($entry->{"distance_{$attr}"}) ?> Δ <b><?= strtoupper($attr) ?></b>
            <?php } else { ?>
                -
            <?php } ?>
        </td>
    <?php } ?>
    <td><?= $entry->correctiontype ?></td>
    <td><?= $entry->display_with_head_posture ?></td>
</tr>