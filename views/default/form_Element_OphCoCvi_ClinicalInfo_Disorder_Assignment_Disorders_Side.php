<?php
    foreach (OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Disorder::model()
                   ->getDisordersWithValuesAndComments($element,$side,$disorder_section['disorder']) as $disorder) {
    ?>
    <fieldset class="row field-row">
        <div class="large-6 column">
            <label><?php echo $disorder['disorder']->name; ?></label>
            <?php echo CHtml::hiddenField("ophcocvi_clinicinfo_disorder_id_".$side."[".$disorder['disorder']->id."]" , $disorder['disorder']->id, array('id' => 'hiddenInput')); ?>
        </div>
        <div class="large-6 column">
            <label class="inline highlight">
                <?php $name = 'affected_'.$side.'['.$disorder_section['disorder']->id.']'.'['.$disorder['disorder']->id.']';
                echo CHtml::radioButton($name, $disorder['status'] == 1 , array('id' => CHtml::modelName($element).$disorder['disorder']->id.'_1', 'value' => 1))?>
                Yes
            </label>
            <label class="inline highlight">
                <?php echo CHtml::radioButton($name, $disorder['status'] == 0, array('id' => CHtml::modelName($element).$disorder['disorder']->id.'_0', 'value' => 0))?>
                No
            </label>
            <?php echo CHtml::hiddenField("ophcocvi_clinicinfo_disorder_id_main_cause_".$side.'['.$disorder_section['disorder']->id.']'."[".$disorder['disorder']->id."]" , $disorder['disorder']->id, array('id' => 'hiddenInput')); ?>
            <?php
            $check_array = array();
            $check_array['value'] = 0;
            if($disorder['value'] == 1) {
                $check_array['checked'] = 'checked';
                $check_array['value'] = 1;
            }?>
            <label class="inline"><?php echo CHtml::checkBox('main_cause_'.$side.'['.$disorder_section['disorder']->id.']'.'['.$disorder['disorder']->id.']', $disorder['value'] == 1 ? true : false ,$check_array);?>
                Main cause
            </label>

        </div>
    </fieldset>
<?php } ?>