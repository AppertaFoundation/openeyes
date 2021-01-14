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
 * @var \OEModule\OphCiExamination\models\Element_OphCiExamination_Refraction $element
 * @var \OEModule\OphCiExamination\widgets\Refraction $this
 */
?>

<div class="element-data element-eyes">
    <?php foreach (['right', 'left'] as $eye_side) { ?>
        <div class="<?=$eye_side?>-eye">
            <?php if ($element->hasEye($eye_side)) { ?>
                <table class="cols-full last-left large">
                    <thead>
                        <tr>
                            <th><?= $this->getReadingAttributeLabel('sphere') ?></th>
                            <th><?= $this->getReadingAttributeLabel('cylinder') ?></th>
                            <th><?= $this->getReadingAttributeLabel('axis') ?></th>
                            <th><?= $this->getReadingAttributeLabel('type') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($element->{"{$eye_side}_readings"} as $reading) { ?>
                            <tr>
                                <td><?= $reading->sphere_display ?></td>
                                <td><?= $reading->cylinder_display ?></td>
                                <td><?= $reading->axis ?></td>
                                <td><?= $reading->type_display ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php if ($element->{"{$eye_side}_notes"}) { ?>
                    <hr class="divider" />
                    <span class="user-comment"><?= $element->textWithLineBreaks("{$eye_side}_notes"); ?></span>
                <?php } ?>
            <?php } else { ?>
                <div class="data-value not-recorded">
                    Not recorded
                </div>
            <?php } ?>
        </div>
    <?php } ?>

</div>