<div class="element-data element-eyes element-fields">
<?php
$pcr = new PcrRisk();
foreach (array('right', 'left') as $side):
    $opposite = ($side === 'right') ? 'left' : 'right';
    $activeClass = ($element->{'has'.ucfirst($side)}()) ? 'active' : 'inactive';
    if(!$element->{$side.'_glaucoma'}){
        continue;
    }
?>
    <div class="element-eye <?=$side?>-eye column <?=$opposite?> side <?=$activeClass?>" data-side="<?=$side?>">
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?php echo $element->getAttributeLabel($side.'_glaucoma')?>:</div>
            </div>
            <div class="large-4 column end">
                <div class="data-value"><?php echo $pcr->displayValues($element->{$side.'_glaucoma'}, 'glaucoma')?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?php echo $element->getAttributeLabel($side.'_pxf')?>:</div>
            </div>
            <div class="large-4 column end">
                <div class="data-value"><?php echo $pcr->displayValues($element->{$side.'_pxf'})?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?php echo $element->getAttributeLabel($side.'_diabetic')?>:</div>
            </div>
            <div class="large-4 column end">
                <div class="data-value"><?php echo $pcr->displayValues($element->{$side.'_diabetic'}, 'diabetic')?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?php echo $element->getAttributeLabel($side.'_pupil_size')?>:</div>
            </div>
            <div class="large-4 column end">
                <div class="data-value"><?php echo $element->{$side.'_pupil_size'}?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?php echo $element->getAttributeLabel($side.'_no_fundal_view')?>:</div>
            </div>
            <div class="large-4 column end">
                <div class="data-value"><?php echo $pcr->displayValues($element->{$side.'_no_fundal_view'})?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?php echo $element->getAttributeLabel($side.'_axial_length_group')?>:</div>
            </div>
            <div class="large-4 column end">
                <div class="data-value"><?php echo $pcr->displayValues($element->{$side.'_axial_length_group'}, 'axial')?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?php echo $element->getAttributeLabel($side.'_brunescent_white_cataract')?>:</div>
            </div>
            <div class="large-4 column end">
                <div class="data-value"><?php echo $pcr->displayValues($element->{$side.'_brunescent_white_cataract'})?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?php echo $element->getAttributeLabel($side.'_alpha_receptor_blocker')?>:</div>
            </div>
            <div class="large-4 column end">
                <div class="data-value"><?php echo $pcr->displayValues($element->{$side.'_alpha_receptor_blocker'})?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?php echo $element->getAttributeLabel($side.'_doctor_grade_id')?>:</div>
            </div>
            <div class="large-4 column end">
                <div class="data-value">
                    <?php
                    if($element->{$side.'_doctor'}) {
                        echo $element->{$side.'_doctor'}->grade;
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?php echo $element->getAttributeLabel($side.'_can_lie_flat')?>:</div>
            </div>
            <div class="large-4 column end">
                <div class="data-value"><?php echo $pcr->displayValues($element->{$side.'_can_lie_flat'})?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column pcr-risk-div">
                <label class="<?php echo $element->pcrRiskColour($side)?>">
                    PCR Risk <span class="pcr-span "><?php echo ($element->{$side.'_pcr_risk'}) ? $element->{$side.'_pcr_risk'} : 'N/A' ?></span> %
                </label>
            </div>
            <div class="large-8 column end">
                <label>
                    Excess risk compared to average eye <span class="pcr-erisk"><?php echo $element->{$side.'_excess_risk'}?></span> times
                </label>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>