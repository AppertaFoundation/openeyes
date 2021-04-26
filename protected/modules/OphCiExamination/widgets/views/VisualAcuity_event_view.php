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
?>

<?php
/**
 * @var \OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity $element
 * @var \OEModule\OphCiExamination\widgets\VisualAcuity $this
 */
?>
<div class="element-data full-width">
    <div class="element-both-eyes">
        <?php if ($readings = $element->beo_readings) { ?>
            <table class="cols-11 last-left">
                <colgroup>
                    <col class="cols-icon">
                    <col>
                    <col>
                    <col>
                    <col>
                    <col class="cols-2">
                    <?php if ($this->readingsHaveFixation()) { ?>
                    <col class="cols-2">
                    <?php } ?>
                    <col class="cols-2">
                    <col class="cols-2">
                </colgroup>
                <thead>
                <tr>
                    <th></th>
                    <th><?= $this->getReadingAttributeLabel('method_id') ?></th>
                    <th><?= $this->getReadingAttributeLabel('unit_id') ?></th>
                    <th></th>
                    <th><?= $this->getReadingAttributeLabel('source_id') ?></th>
                    <?php if ($this->readingsHaveFixation()) { ?>
                        <th><?= $this->getReadingAttributeLabel('fixation_id') ?></th>
                    <?php } ?>
                    <th><?= $this->getReadingAttributeLabel('occluder_id') ?></th>
                    <th><?=  $this->getReadingAttributeLabel('with_head_posture') ?></th>
                </tr>
                </thead>
                <tbody>
                <?= $this->renderReadingsForElement($readings) ?>
                </tbody>
            </table>
        <?php } else { ?>
            <div class="data-value not-recorded">
                <?= $element->getNotRecordedTextForSide('beo') ?>
            </div>
        <?php }
        if ($element->beo_notes) { ?>
            <hr class="divider" />
            <span class="user-comment"><?= $element->textWithLineBreaks("beo_notes"); ?></span>
        <?php } ?>

    </div>
    <div class="element-eyes">
        <?php foreach (['right', 'left'] as $eye_side) { ?>
            <div class="<?= $eye_side ?>-eye">
                <?php if ($side_readings = $element->{"{$eye_side}_readings"}) { ?>
                    <table class="cols-full last-left">
                        <colgroup>
                            <col>
                            <col>
                            <col>
                            <col>
                            <col class="cols-2">
                            <?php if ($this->readingsHaveFixation()) { ?>
                                <col class="cols-2">
                            <?php } ?>
                            <col class="cols-2">
                            <col class="cols-2">
                        </colgroup>
                        <thead>
                            <tr>
                                <th><?= $this->getReadingAttributeLabel('method_id') ?></th>
                                <th><?= $this->getReadingAttributeLabel('unit_id') ?></th>
                                <th></th>
                                <th><?= $this->getReadingAttributeLabel('source_id') ?></th>
                                <?php if ($this->readingsHaveFixation()) { ?>
                                    <th><?= $this->getReadingAttributeLabel('fixation_id') ?></th>
                                <?php } ?>
                                <th><?= $this->getReadingAttributeLabel('occluder_id') ?></th>
                                <th><?=  $this->getReadingAttributeLabel('with_head_posture') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?= $this->renderReadingsForElement($side_readings) ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <div class="data-value not-recorded">
                        <?= $element->getNotRecordedTextForSide($eye_side) ?>
                    </div>
                <?php } ?>
                <?php if ($element->{"{$eye_side}_notes"}) { ?>
                    <hr class="divider" />
                    <span class="user-comment"><?= $element->textWithLineBreaks("{$eye_side}_notes"); ?></span>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</div>
