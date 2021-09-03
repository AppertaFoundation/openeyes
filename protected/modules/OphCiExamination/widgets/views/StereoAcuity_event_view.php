<?php

/**
 * @var \OEModule\OphCiExamination\models\StereoAcuity $element
 * @var \OEModule\OphCiExamination\widgets\StereoAcuity $this
 */

?>
<div class="element-data full-width">
    <div class="pro-data-view">

        <div id="js-listview-stereoacuity-pro" class="listview-pro">
            <ul class="dot-list large">
                <?php foreach ($element->entries as $entry) { ?>
                    <li><?= $entry ?></li>
                <?php } ?>
            </ul>
        </div>
        <div id="js-listview-stereoacuity-full" class="listview-full column cols-10" style="display: none;">
            <table class="cols-10 last-left">
                <colgroup>
                    <col class="cols-3">
                    <col class="cols-3">
                    <col class="cols-3">
                    <col class="cols-3">
                </colgroup>
                <thead>
                <th><?= $this->getReadingAttributeLabel('method_id') ?></th>
                <th><?= $this->getReadingAttributeLabel('result') ?></th>
                <th><?= $this->getReadingAttributeLabel('correctiontype_id') ?></th>
                <th><?= $this->getReadingAttributeLabel('with_head_posture') ?></th>
                </thead>
                <tbody>
                <?php foreach ($element->entries as $entry) { ?>
                <tr>
                    <td><?= $entry->method ?? '-' ?></td>
                    <td><?= $entry->display_result ?? '-' ?></td>
                    <td><?= $entry->correctiontype ?? '-' ?></td>
                    <td><?= $entry->display_with_head_posture ?? '-' ?></td>
                </tr>
                <?php } ?>
                </tbody>
            </table>

        </div>
        <div>
            <i class="oe-i small js-listview-expand-btn expand" data-list="stereoacuity"></i>
        </div>
    </div>
</div>