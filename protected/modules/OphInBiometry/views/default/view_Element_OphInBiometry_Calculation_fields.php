<div class="data-value">
    <table class="large last-left">
        <colgroup>
            <col class="cols-6" span="2">
        </colgroup>
        <tr>
            <td>
                <b><?php echo CHtml::encode($element->getAttributeLabel('comments_' . $side)) ?></b>
            </td>
            <td class="field-info<?php if ($element->{'comments_' . $side}) {
                ?> iolDisplay<?php
                                 } ?>"
                style="word-break: break-word;">
                <?php echo CHtml::encode($element->{'comments_' . $side}) ?>
            </td>
        </tr>
        <tr>
            <td>
                <b><?php echo CHtml::encode($element->getAttributeLabel('target_refraction_' . $side)) ?></b>
            </td>
            <td>
                <?php if ($element->{'target_refraction_' . $side}) {
                    echo CHtml::encode($element->{'target_refraction_' . $side});
                } else {
                    $csm_refraction = \OEModule\OphCiExamination\models\Element_OphCiExamination_CataractSurgicalManagement::getLatestTargetRefraction($this->patient, $side);
                    echo $csm_refraction ? CHtml::encode($csm_refraction) : 'Not recorded';
                } ?>
            </td>
        </tr>
    </table>
</div>