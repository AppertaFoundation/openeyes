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
                <?php echo CHtml::encode($element->{'target_refraction_' . $side}) ?>
            </td>
        </tr>
    </table>
</div>