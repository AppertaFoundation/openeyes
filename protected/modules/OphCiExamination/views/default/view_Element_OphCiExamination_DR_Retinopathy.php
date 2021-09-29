<?php
use OEModule\OphCiExamination\models\Element_OphCiExamination_DR_Retinopathy;

/**
 * @var $element Element_OphCiExamination_DR_Retinopathy
 */
?>
<div class="element-data element-eyes">
    <?php foreach (array('left' => 'right', 'right' => 'left') as $page_side => $eye_side) { ?>
    <div class="<?= $eye_side ?>-eye" data-side="<?= $eye_side ?>">
        <?php if ($element->hasEye($eye_side)) {?>
            <div class="data-value">
                <table class="label-value last-left">
                    <colgroup>
                        <col class="cols-3"/>
                    </colgroup>
                    <tbody>
                    <?php foreach ($element->{$eye_side . '_retinopathy_features'} as $retinopathy_feature) {?>
                        <tr>
                            <td>
                                <?= $retinopathy_feature->feature->grade ?>
                            </td>
                            <td>
                                <?= $retinopathy_feature->feature_count ? $retinopathy_feature->feature_count . ' ' : null ?><?= $retinopathy_feature->feature->name ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="data-value not-recorded">
                Not recorded
            </div>
        <?php } ?>
    </div>
    <?php } ?>
</div>
