<?php

/**
 * @var \OEModule\OphCiExamination\models\CoverAndPrismCover $element
 * @var \OEModule\OphCiExamination\widgets\CoverAndPrismCover $this
 */

$prefix_attributes = ['with_head_posture'];
?>
<div class="element-data full-width">
    <div class="pro-data-view">

        <div id="js-listview-coverandprismcover-pro"
             class="listview-pro"
             style="<?= $this->shouldDisplayProViewForEntries($element->entries) ? "" : "display: none;" ?>">
            <ul class="dot-list large">
                <?php foreach ($element->entries as $entry) { ?>
                    <li><?= $entry ?></li>
                <?php } ?>
            </ul>
            <?php if ($element->comments) { ?>
                <table class="cols-full last-left" <?php /* ugly style hack! */ ?>
                       style="border-top: 1px solid #a0a0c8;">
                    <tbody>
                    <tr>
                        <td><?= Yii::app()->format->Ntext($element->comments) ?? '-' ?></td>
                    </tr>
                    </tbody>
                </table>
            <?php } ?>
        </div>
        <div id="js-listview-coverandprismcover-full" class="listview-full column cols-12" style="<?= $this->shouldDisplayProViewForEntries($element->entries) ? "display: none;" : "" ?>">
            <table class="cols-10 last-left">
                <colgroup>
                    <col class="cols-2">
                    <col class="cols-2">
                    <col class="cols-3">
                    <col class="cols-1">
                    <col class="cols-1">
                    <col class="cols-2">
                </colgroup>
                <thead>
                <th><?= $this->getReadingAttributeLabel('distance_id') ?></th>
                <th><?= $this->getReadingAttributeLabel('correctiontype_id') ?></th>
                <th><?= $this->getReadingAttributeLabel('comments') ?></th>
                <th>Horizontal</th>
                <th>Vertical</th>
                <th><?= $this->getReadingAttributeLabel('with_head_posture') ?></th>
                </thead>
                <tbody>
                <?php foreach ($element->entries as $entry) { ?>
                    <tr>
                        <td><?= $entry->distance ?? '-' ?></td>
                        <td><?= $entry->correctiontype ?? '-' ?></td>
                        <td><?= OELinebreakReplacer::replace(CHtml::encode($entry->comments ?? '-')) ?></td>
                        <td class="nowrap"><?= $entry->horizontal_prism ? CHtml::encode($entry->horizontal_value) . " Δ " . $entry->horizontal_prism : "-" ?></td>
                        <td class="nowrap"><?= $entry->vertical_prism ? CHtml::encode($entry->vertical_value) . " Δ " . $entry->vertical_prism : "-" ?></td>
                        <td><?= $entry->display_with_head_posture ?></td>
                    </tr>
                <?php } ?>

                <?php if ($element->comments) { ?>
                    <tr>
                        <td colspan="6"><?=OELinebreakReplacer::replace(CHtml::encode($element->comments ?? '-')) ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <div>
            <i class="oe-i small js-listview-expand-btn expand" data-list="coverandprismcover"></i>
        </div>
    </div>
</div>