<?php

/**
 * @var \OEModule\OphCiExamination\models\Synoptophore $element
 * @var \OEModule\OphCiExamination\widgets\Synoptophore $this
 */

?>

<div class="element-data full-width">

        <div class="element-both-eyes">
            <div class="cols-2">
                <?= $this->element->getAttributeLabel('angle_from_primary') ?>: <?= $element->angle_from_primary ?>°
            </div>
            <?php if ($element->comments) { ?>
            <div class="cols-10">
                <span class="user-comment"><?= Yii::app()->format->Ntext($element->comments) ?? '-' ?></span>
            </div>
            <?php } ?>
        </div><!-- .element-both-eyes -->

    <!-- now split for R / L eye data -->
    <div class="element-eyes">
        <?php foreach (['right', 'left'] as $eye_side) { ?>
        <div class="<?= $eye_side ?>-eye">
            <?php if ($element->hasEye($eye_side)) { ?>
                <div class="data-value">
                    <?= ucfirst($eye_side); ?> Eye Fixation
                    <table class="cols-full last-left">
                        <colgroup>
                            <col class="cols-4" span="3">
                        </colgroup>
                        <tbody>
                        <?php foreach ($this->getAllReadingGazeTypes() as $row) { ?>
                        <tr class="col-gap">
                            <?php foreach ($row as $gaze_type) { ?>
                                <td class="nowrap"><?= CHtml::encode($element->getReadingForSideByGazeType($eye_side, $gaze_type)) ?></td>
                            <?php } ?>
                        </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } else { ?>
                <div class="data-value not-recorded">
                    Not recorded
                </div>
            <?php } ?>
        </div>
        <?php } ?>
    </div><!-- .element-eyes -->
</div>