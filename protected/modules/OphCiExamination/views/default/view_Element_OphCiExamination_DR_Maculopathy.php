<?php
use OEModule\OphCiExamination\models\Element_OphCiExamination_DR_Maculopathy;

/**
 * @var $element Element_OphCiExamination_DR_Maculopathy
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
                    <?php foreach ($element->{$eye_side . '_maculopathy_features'} as $maculopathy_feature) {?>
                        <tr>
                            <td>
                                <?= $maculopathy_feature->feature->grade ?>
                            </td>
                            <td>
                                <?= $maculopathy_feature->feature->name ?>
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
