<?php
foreach (OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Disorder_Section::model()
             ->findAll('`active` = ?', array(1)) as $disorder_section) {
    $comments = OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo_Disorder_Section_Comments::model()
        ->getDisorderSectionComments($disorder_section->id,$element->id);
    $flag = 0;
    foreach (OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Disorder::model()
    ->findAll('`active` = ? and section_id = ?', array(1, $disorder_section->id)) as $disorder) {
        $value1 = OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo_Disorder_Assignment::model()->
        getDisorderAffectedStatus($disorder->id, $element->id, 'left');
        $value2 = OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo_Disorder_Assignment::model()->
        getDisorderAffectedStatus($disorder->id, $element->id, 'right');
        if ($value1 == 1 || $value2 == 1) {
            $flag = 1;
            break;
        }
    }
    if($comments !== '' || $flag == 1) {
        ?>
        <fieldset class="row field-row">
            <legend class="large-12 column">
                <?php echo $disorder_section->name; ?>
            </legend>
        </fieldset>
        <?php
    }
    if($flag == 1) {
        ?>
        <div class="row data-row">
            <div class="large-12 column end">
                <div class="sub-element-data sub-element-eyes row">
                    <div class="element-eye right-eye column">
                        <?php $this->renderPartial('view_Element_OphCoCvi_ClinicalInfo_Disorder_Assignment_Disorders_Side', array(
                            'side' => 'right',
                            'element' => $element,
                            'disorder_section' => $disorder_section,
                        )) ?>
                    </div>
                    <div class="element-eye left-eye column">
                        <?php $this->renderPartial('view_Element_OphCoCvi_ClinicalInfo_Disorder_Assignment_Disorders_Side', array(
                            'side' => 'left',
                            'element' => $element,
                            'disorder_section' => $disorder_section,
                        )) ?>
                    </div>
                </div>
            </div>
        </div>

        <?php
    }
    if($disorder_section->comments_allowed == 1){
        if ($comments != '') { ?>
        <fieldset class="row field-row">
            <legend class="large-4 column">
                <?php echo $disorder_section->comments_label; ?>
            </legend>
            <div class="large-8 column">
                <?php echo $comments;?>
            </div>
        </fieldset>
    <?php }
    }
}
?>
