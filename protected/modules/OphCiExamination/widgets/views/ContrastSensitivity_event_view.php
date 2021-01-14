<?php

/**
 * @var \OEModule\OphCiExamination\models\ContrastSensitivity $element
 * @var \OEModule\OphCiExamination\widgets\ContrastSensitivity $this
 */

use OEModule\OphCiExamination\models\ContrastSensitivity_Result;
?>
<div class="element-data full-width">

    <div  class="cols-10">
        <table class="last-left">
            <colgroup>
                <col class="cols-3">
                <col class="cols-2">
                <col class="cols-2">
                <col class="cols-3">
            </colgroup>
            <thead>
                <th><?= $this->getResultAttributeLabel('contrastsensitivity_type_id') ?></th>
                <th><?= $this->getResultAttributeLabel('value') ?></th>
                <th><?= $this->getResultAttributeLabel('eye_id') ?></th>
                <th><?= $this->getResultAttributeLabel('correctiontype_id') ?></th>
            </thead>
            <tbody>
            <?php foreach ($element->results as $result) { ?>
            <tr>
                <td><?= $result->contrastsensitivity_type ?? '-' ?></td>
                <td><?= CHtml::encode($result->value ?? '-') ?></td>
                <td><span class="oe-eye-lat-icons">
                        <?php
                        if ((string)$result->eye_id === (string)ContrastSensitivity_Result::RIGHT) {
                            ?><i class="oe-i laterality R small pad"></i><?php
                        }
                        if (
                            (string)$result->eye_id === (string)ContrastSensitivity_Result::LEFT ||
                            (string)$result->eye_id === (string)ContrastSensitivity_Result::RIGHT
                        ) {
                            ?><i class="oe-i NA small pad"></i><?php
                        }
                        if ((string)$result->eye_id === (string)ContrastSensitivity_Result::LEFT) {
                            ?><i class="oe-i laterality L small pad"></i><?php
                        } elseif ((string)$result->eye_id === (string)ContrastSensitivity_Result::BEO) {
                            ?><i class="oe-i small pad"></i><i class="oe-i beo small pad"></i><?php
                        }
                        ?>
                    </span></td>
                <td><?= $result->correctiontype ?? '-' ?></td>
            </tr>
            <?php } ?>

            <?php if ($element->comments) { ?>
            <tr>
                <td colspan="4"><?= OELinebreakReplacer::replace(CHtml::encode($element->comments)) ?></td>
            </tr>
            <?php }; ?>
            </tbody>
        </table>
    </div>
</div>