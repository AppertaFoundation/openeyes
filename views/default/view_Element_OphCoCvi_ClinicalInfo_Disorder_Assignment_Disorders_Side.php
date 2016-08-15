<?php
foreach (OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Disorder::model()
             ->findAll('`active` = ? and section_id = ?', array(1, $disorder_section->id)) as $disorder) {
    $value = OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo_Disorder_Assignment::model()->
    getDisorderAffectedStatus($disorder->id, $element->id, $side);
    if ($value == 1) {

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
}?>