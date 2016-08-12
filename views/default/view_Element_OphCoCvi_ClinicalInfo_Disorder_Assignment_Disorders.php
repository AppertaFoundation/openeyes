<?php
foreach (OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Disorder_Section::model()
             ->findAll('`active` = ?', array(1)) as $disorder_section) {
    $flag = 0;
    foreach (OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Disorder::model()
                       ->findAll('`active` = ? and section_id = ?', array(1, $disorder_section->id)) as $disorder) {
        $value = OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo_Disorder_Assignment::model()->
        getDisorderAffectedStatus($disorder->id, $element->id, $side);
        if ($value == 1) {
            if ($flag == 0) { ?>
                    <fieldset class="row field-row">
                        <legend class="large-12 column">
                            <?php echo $disorder_section->name; ?>
                        </legend>
                    </fieldset>
                    <?php
            }
            $flag = 1;
            $checkbox_value = OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo_Disorder_Assignment::model()->
            getDisorderMainCause($disorder->id, $element->id, $side);
            ?>
            <div class="row data-row">
                <div class="large-12 column">
                    <div class="data-label">
                        <?php echo $disorder->name;
                        if ($checkbox_value == 1) {
                            echo ' - Main Cause';
                        }
                        ?>
                    </div>
                </div>
            </div>
        <?php }
    }
    $comments = OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo_Disorder_Section_Comments::model()
        ->getDisorderSectionComments($disorder_section->id,$element->id,$side);
    if($disorder_section->comments_allowed == 1){
        if ($comments != '') {
        if ($flag == 0) { ?>
        <fieldset class="row field-row">
            <legend class="large-12 column">
                <?php echo $disorder_section->name; ?>
            </legend>
        </fieldset>
        <?php
        }
        ?>
        <fieldset class="row field-row">
            <legend class="large-6 column">
                <?php echo $disorder_section->comments_label; ?>
            </legend>
            <div class="large-6 column">
                <?php echo $comments;?>
            </div>
        </fieldset>
    <?php }
    }
}
?>
