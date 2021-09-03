<?php

/**
 * @var \OEModule\OphCiExamination\models\ConvergenceAccommodation $element
 * @var \OEModule\OphCiExamination\widgets\ConvergenceAccommodation $this
 */

$pro_attr_display_map = [
    'correctiontype'    => 'correctiontype',
    'with_head_posture' => 'display_with_head_posture',
    'comments' => 'comments',
];
$prefix_attributes = ['with_head_posture'];
?>
<div class="element-data full-width">
    <div class="pro-data-view">

        <div id="js-listview-convergenceaccommodation-pro" class="listview-pro">
            <ul class="dot-list large">
                <?php foreach ($pro_attr_display_map as $attr => $display_attr) {
                    if (isset($element->$attr)) { ?>
                        <li><?= (in_array($attr, $prefix_attributes) ? $element->getAttributeLabel($attr) . ": " : "") . $element->$display_attr ?></li>
                    <?php }
                } ?>
            </ul>
        </div>
        <div id="js-listview-convergenceaccommodation-full" class="listview-full column cols-10" style="display: none;">
            <table class="cols-10 last-left">
                <colgroup>
                    <col class="cols-2">
                    <col class="cols-2">
                    <col class="cols-4">
                </colgroup>
                <thead>
                <th><?= $element->getAttributeLabel('correctiontype_id') ?></th>
                <th><?= $element->getAttributeLabel('with_head_posture') ?></th>
                <th><?= $element->getAttributeLabel('comments') ?></th>
                </thead>
                <tbody>
                <tr>
                    <td><?= $element->correctiontype ?? '-' ?></td>
                    <td><?= $element->display_with_head_posture ?? '-' ?></td>
                    <td><?= OELinebreakReplacer::replace(CHtml::encode($element->comments)) ?></td>
                </tr>
                </tbody>
            </table>

        </div>
        <div>
            <i class="oe-i small js-listview-expand-btn expand" data-list="convergenceaccommodation"></i>
        </div>
    </div>
</div>