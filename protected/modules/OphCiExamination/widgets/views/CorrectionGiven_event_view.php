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

/**
 * @var \OEModule\OphCiExamination\models\CorrectionGiven $element
 * @var \OEModule\OphCiExamination\widgets\CorrectionGiven $this
 */
?>

<div class="element-data element-eyes">
    <?php foreach (['right', 'left'] as $side) { ?>
    <div class="<?= $side ?>-eye" data-side="<?= $side ?>">
        <?php if ($element->hasEye($side)) { ?>
            <table class="cols-full last-left">
                <colgroup>
                    <col class="cols-6" span="2">
                </colgroup>
                <tbody>
                <tr>
                    <td><?= $element->getOrderLabelForSide($side) ?></td>
                    <td><?= CHtml::encode($element->{"{$side}_refraction"}) ?></td>
                </tr>
                </tbody>
            </table>
        <?php } else { ?>
            <div class="data-value not-recorded" data-test="<?= "correction-given-{$side}-not-recorded" ?>">
                Not recorded
            </div>
        <?php } ?>
    </div>
    <?php } ?>
</div>
