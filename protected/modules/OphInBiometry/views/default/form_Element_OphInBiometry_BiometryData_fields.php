
<div class="element-fields">
    <div id="div_Element_OphInBiometry_BiometryData_axial_length_<?php echo $side?>" class="data-group">
        <div class="cols-3 column">
            <label for="Element_OphInBiometry_BiometryData_axial_length_<?php echo $side?>">
                <?php echo $element->getAttributeLabel('axial_length_'.$side)?>
            </label>
        </div>
        <div class="cols-3 column">
            <?php echo $form->textField($element, 'axial_length_'.$side, array('nowrapper' => true, 'append-text' => 'mm'))?>
        </div>
        <div class="cols-4 column end">
            <?php echo $form->textField($element, 'snr_'.$side, array('prepend-text' => 'SNR =', 'nowrapper' => true))?>
        </div>
    </div>
    <div id="div_Element_OphInBiometry_BiometryData_r1_<?php echo $side?>" class="data-group">
        <div class="cols-3 column">
            <label for="Element_OphInBiometry_BiometryData_r1_<?php echo $side?>">
                <?php echo $element->getAttributeLabel('r1_'.$side)?>
            </label>
        </div>
        <div class="cols-3 column">
            <?php echo $form->textField($element, 'r1_'.$side, array('nowrapper' => true, 'append-text' => 'mm'))?>
        </div>
        <div class="cols-5 column end">
            <?php echo $form->textField($element, 'r1_axis_'.$side, array('nowrapper' => true, 'prepend-text' => '0 D @', 'append-text' => '°'))?>
        </div>
    </div>
    <div id="div_Element_OphInBiometry_BiometryData_r2_<?php echo $side?>" class="data-group">
        <div class="cols-3 column">
            <label for="Element_OphInBiometry_BiometryData_r2_<?php echo $side?>">
                <?php echo $element->getAttributeLabel('r2_'.$side)?>
            </label>
        </div>
        <div class="cols-3 column">
            <?php echo $form->textField($element, 'r2_'.$side, array('nowrapper' => true, 'append-text' => 'mm'))?>
        </div>
        <div class="cols-5 column end">
            <?php echo $form->textField($element, 'r2_axis_'.$side, array('nowrapper' => true, 'prepend-text' => '0 D @', 'append-text' => '°'))?>
        </div>
    </div>
    <div id="div_Element_OphInBiometry_BiometryData_rse_<?php echo $side?>" class="data-group">
        <div class="cols-3 column">
            <label for="Element_OphInBiometry_BiometryData_rse_<?php echo $side?>">
                R/SE:
            </label>
        </div>
        <div class="cols-3 column">
            <label class="rse_mm_<?php echo $side?>"></label>
        </div>
        <div class="cols-5 column end">
            <label class="rse_d_<?php echo $side?> prepend"></label>
        </div>
    </div>
    <div id="div_Element_OphInBiometry_BiometryData_cyl_<?php echo $side?>" class="data-group">
        <div class="cols-3 column">
            <label for="Element_OphInBiometry_BiometryData_cyl_<?php echo $side?>">
                Cyl:
            </label>
        </div>
        <div class="cols-3 column end">
            <label class="cyl_<?php echo $side?>"></label>
        </div>
    </div>
  <div class="cols-12 column">
        <?php echo $form->textField($element, 'acd_'.$side, array('append-text' => 'mm'), null, array('label' => 3, 'field' => 3))?>
            <?php echo $form->textField($element, 'scleral_thickness_'.$side, array('append-text' => 'mm'), null, array('label' => 3, 'field' => 3))?>
  </div>
</div>
