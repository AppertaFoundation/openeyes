<?php

/**
 * @var \OEModule\OphCiExamination\models\HeadPosture $element
 * @var \OEModule\OphCiExamination\widgets\HeadPosture $this
 */

$pro_attr_display_map = [
    'tilt'     => 'display_tilt',
    'turn'     => 'display_turn',
    'chin'     => 'display_chin',
    'comments' => 'comments',
];
$prefix_attributes = ['tilt', 'turn', 'chin'];
?>
<div class="element-data full-width">
    <div class="pro-data-view">

        <div id="js-listview-headposture-pro" class="listview-pro">
            <ul class="dot-list large">
                <?php foreach ($pro_attr_display_map as $attr => $display_attr) {
                    if (isset($element->$attr)) { ?>
                        <li><?= (in_array($attr, $prefix_attributes) ? $element->getAttributeLabel($attr) . ": " : "") . $element->$display_attr ?></li>
                    <?php }
                } ?>
            </ul>
        </div>
        <div id="js-listview-headposture-full" class="listview-full column cols-10" style="display: none;">
            <table class="cols-10 last-left">
                <colgroup>
                    <col class="cols-2">
                    <col class="cols-2">
                    <col class="cols-2">
                    <col class="cols-4">
                </colgroup>
                <thead>
                <th><?= $element->getAttributeLabel('tilt') ?></th>
                <th><?= $element->getAttributeLabel('turn') ?></th>
                <th><?= $element->getAttributeLabel('chin') ?></th>
                <th><?= $element->getAttributeLabel('comments') ?></th>
                </thead>
                <tbody>
                <tr>
                    <td><?= $element->display_tilt ?? '-' ?></td>
                    <td><?= $element->display_turn ?? '-' ?></td>
                    <td><?= $element->display_chin ?? '-' ?></td>
                    <td><?= OELinebreakReplacer::replace(CHtml::encode($element->comments ?? '-')) ?></td>
                </tr>
                </tbody>
            </table>

        </div>
        <div>
            <i class="oe-i small js-listview-expand-btn expand" data-list="headposture"></i>
        </div>
    </div>
</div>