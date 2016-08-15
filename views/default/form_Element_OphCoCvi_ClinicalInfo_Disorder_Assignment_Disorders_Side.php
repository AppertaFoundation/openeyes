<?php
    foreach (OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Disorder::model()
                   ->findAll('`active` = ? and section_id = ?',array(1, $disorder_section->id)) as $disorder) {

    $value = OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo_Disorder_Assignment::model()->getDisorderAffectedStatus($disorder->id,$element->id,$side);
    $checkbox_value = OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo_Disorder_Assignment::model()->getDisorderMainCause($disorder->id,$element->id,$side);
    //$comments = $element1->getComments($factor->id,$element->id);
    ?>
    <fieldset class="row field-row">
        <legend class="large-6 column">
            <?php echo $disorder->name; ?>
            <?php echo CHtml::hiddenField("ophcocvi_clinicinfo_disorder_id_".$side."[".$disorder->id."]" , $disorder->id, array('id' => 'hiddenInput')); ?>
        </legend>
        <div class="large-6 column">
            <label class="inline highlight">
                <?php $name = 'affected_'.$side.'['.$disorder_section->id.']'.'['.$disorder->id.']';
                echo CHtml::radioButton($name, $value == 1 , array('id' => CHtml::modelName($element).$disorder->id.'_1', 'value' => 1))?>
                Yes
            </label>
            <label class="inline highlight">
                <?php echo CHtml::radioButton($name, $value == 0, array('id' => CHtml::modelName($element).$disorder->id.'_0', 'value' => 0))?>
                No
            </label>
        </div>
    </fieldset>
    <fieldset class="row field-row">
        <legend class="large-6 column">
            <?php echo 'Main Cause'; ?>
            <?php echo CHtml::hiddenField("ophcocvi_clinicinfo_disorder_id_main_cause_".$side.'['.$disorder_section->id.']'."[".$disorder->id."]" , $disorder->id, array('id' => 'hiddenInput')); ?>
        </legend>
        <div class="large-6 column">
            <?php
            $check_array = array();
            $check_array['value'] = 0;
            if($checkbox_value == 1) {
                $check_array['checked'] = 'checked';
                $check_array['value'] = 1;
            }
            echo CHtml::checkBox('main_cause_'.$side.'['.$disorder_section->id.']'.'['.$disorder->id.']',$checkbox_value == 1 ? true : false ,$check_array);
            ?>
        </div>
    </fieldset>
<?php } ?>