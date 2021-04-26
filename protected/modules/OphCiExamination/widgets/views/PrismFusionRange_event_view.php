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

/** @var \OEModule\OphCiExamination\models\PrismFusionRange $element */
/** @var \OEModule\OphCiExamination\widgets\PrismFusionRange $this */
?>

<div class="element-data full-width">
    <div class="cols-full">
        <table class="last-left">
            <thead>
            <tr class="no-line">
                <th><?= $this->getReadingAttributeLabel('prism_over_eye_id') ?></th>
                <th colspan="4">Near</th>
                <th colspan="4">Distance</th>
                <th><?= $this->getReadingAttributeLabel('correctiontype_id') ?></th>
                <th><?= $this->getReadingAttributeLabel('with_head_posture') ?></th>
                <th><!-- trash --></th>
            </tr>
            </thead>
            <tbody>
            <?= $this->renderEntriesForElement($element->entries) ?>
            </tbody>
        </table>
    </div>
    <?php if (!empty($element->comments)) { ?>
    <hr class="divider">
    <div><span class="user-comment"><?= OELinebreakReplacer::replace(CHtml::encode($element->comments)) ?></span></div>
    <?php } ?>
</div>
