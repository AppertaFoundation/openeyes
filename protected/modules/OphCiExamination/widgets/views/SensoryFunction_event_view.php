<?php

/**
 * @var \OEModule\OphCiExamination\models\SensoryFunction $element
 * @var \OEModule\OphCiExamination\widgets\SensoryFunction $this
 */

?>
<div class="element-data full-width">
    <div class="pro-data-view">

        <div id="js-listview-sensoryfunction-pro"
             class="listview-pro"
             style="<?= $this->shouldDisplayProViewForEntries($element->entries) ? "" : "display: none;" ?>">
            <ul class="dot-list large">
                <?php foreach ($element->entries as $entry) { ?>
                    <li><?= $entry ?></li>
                <?php } ?>
            </ul>
        </div>
        <div id="js-listview-sensoryfunction-full"
             class="listview-full column cols-10"
             style="<?= $this->shouldDisplayProViewForEntries($element->entries) ? "display: none;" : "" ?>">
            <table class="cols-10 last-left">
                <colgroup>
                    <col class="cols-2">
                    <col class="cols-2">
                    <col class="cols-2">
                    <col class="cols-2">
                    <col class="cols-2">
                </colgroup>
                <thead>
                <th><?= $this->getEntryAttributeLabel('entry_type_id') ?></th>
                <th><?= $this->getEntryAttributeLabel('distance_id') ?></th>
                <th><?= $this->getEntryAttributeLabel('correctiontypes') ?></th>
                <th><?= $this->getEntryAttributeLabel('result_id') ?></th>
                <th><?= $this->getEntryAttributeLabel('with_head_posture') ?></th>
                </thead>
                <tbody>
                <?php foreach ($element->entries as $entry) { ?>
                    <tr>
                        <td><?= $entry->entry_type ?? '-' ?></td>
                        <td><?= $entry->distance ?? '-' ?></td>
                        <td><?= $entry->display_correctiontypes ?? '-' ?></td>
                        <td><?= $entry->result ?? '-' ?></td>
                        <td><?= $entry->display_with_head_posture ?? '-' ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>

        </div>
        <div>
            <i class="oe-i small js-listview-expand-btn <?= $this->shouldDisplayProViewForEntries($element->entries) ? "expand" : "collapse" ?>"
               data-list="sensoryfunction"></i>
        </div>
    </div>
</div>