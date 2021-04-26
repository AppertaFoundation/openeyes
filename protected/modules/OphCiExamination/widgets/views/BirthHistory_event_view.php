<?php

/**
 * @var \OEModule\OphCiExamination\models\BirthHistory $element
 * @var \OEModule\OphCiExamination\widgets\BirthHistory $this
 */

$pro_attr_display_map = [
    'weight_recorded_units' => 'display_weight',
    'delivery_type'         => 'delivery_type',
    'gestation_weeks'       => 'display_gestation_weeks',
    'had_neonatal_specialist_care' => 'display_labelled_had_neonatal_specialist_care',
    'was_multiple_birth'           => 'display_labelled_was_multiple_birth'
];
?>
<div class="element-data full-width">
    <div class="pro-data-view">

        <div id="js-listview-birthhistory-pro" class="listview-pro">
            <ul class="dot-list large">
                <?php foreach ($pro_attr_display_map as $attr => $display_attr) {
                    if (isset($element->$attr)) { ?>
                        <li><?= $element->$display_attr ?></li>
                    <?php }
                } ?>
            </ul>
        </div>
        <div id="js-listview-birthhistory-full" class="listview-full column cols-10" style="display: none;">
            <table class="cols-10 last-left">
                <colgroup>
                    <col class="cols-3">
                    <col class="cols-3">
                    <col class="cols-2">
                    <col class="cols-2">
                    <col class="cols-2">
                </colgroup>
                <thead>
                <th><?= $element->getAttributeLabel('weight') ?></th>
                <th><?= $element->getAttributeLabel('birth_history_delivery_type_id') ?></th>
                <th><?= $element->getAttributeLabel('gestation_weeks') ?></th>
                <th><?= $element->getAttributeLabel('had_neonatal_specialist_care') ?></th>
                <th><?= $element->getAttributeLabel('was_multiple_birth') ?></th>
                </thead>
                <tbody>
                <tr>
                    <td><?= CHtml::encode($element->display_weight ?? '-') ?></td>
                    <td><?= $element->delivery_type ?? '-' ?></td>
                    <td><?= CHtml::encode($element->display_gestation_weeks ?? '-') ?></td>
                    <td><?= $element->display_had_neonatal_specialist_care ?? '-' ?></td>
                    <td><?= $element->display_was_multiple_birth ?? '-' ?></td>
                </tr>
                </tbody>
            </table>

        </div>
        <div>
            <i class="oe-i small js-listview-expand-btn expand" data-list="birthhistory"></i>
        </div>
    </div>
    <?php if (!empty($element->comments)) { ?>
        <hr class="divider">
        <div><span class="user-comment"><?= OELinebreakReplacer::replace(CHtml::encode($element->comments)) ?></span></div>
    <?php } ?>
</div>