<?php

/**
 * @var \OEModule\OphCiExamination\models\PrismReflex $element
 * @var \OEModule\OphCiExamination\widgets\PrismReflex $this
 */

$prefix_attributes = ['with_head_posture'];
?>
<div class="element-data full-width">
    <div class="pro-data-view">

        <div id="js-listview-prismreflex-pro" class="listview-pro">
            <table class="last-left">
                <tbody>
                <?php foreach ($element->entries as $entry) { ?>
                    <tr>
                        <td><?= $entry ?></td>
                    </tr>
                <?php } ?>
                </tbody>

            </table>
        </div>
        <div id="js-listview-prismreflex-full" class="listview-full column cols-10" style="display: none;">
            <table class="cols-10 last-left">
                <colgroup>
                    <col class="cols-2">
                    <col class="cols-2">
                    <col class="cols-2">
                    <col class="cols-2">
                    <col class="cols-2">
                </colgroup>
                <thead>
                    <th><?= $this->getReadingAttributeLabel('prismdioptre_id') ?></th>
                    <th><?= $this->getReadingAttributeLabel('correctiontype_id') ?></th>
                    <th><?= $this->getReadingAttributeLabel('prismbase_id') ?></th>
                    <th><?= $this->getReadingAttributeLabel('finding_id') ?></th>
                    <th><?= $this->getReadingAttributeLabel('with_head_posture') ?></th>
                </thead>
                <tbody>
                <?php foreach ($element->entries as $entry) { ?>
                <tr>
                    <td><?= $entry->prismdioptre ?? '-' ?></td>
                    <td><?= $entry->correctiontype ?? '-' ?></td>
                    <td><?= $entry->prismbase ?? '-' ?></td>
                    <td><?= $entry->finding ?? '-' ?></td>
                    <td><?= $entry->display_with_head_posture ?? '-' ?></td>
                </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <div>
            <i class="oe-i small js-listview-expand-btn expand" data-list="prismreflex"></i>
        </div>
    </div>
    <?php if (!empty($element->comments)) { ?>
        <hr class="divider" />
        <div><span class="user-comment"><?= OELinebreakReplacer::replace(CHtml::encode($element->comments)) ?></span></div>
    <?php } ?>
</div>