<?php
foreach ($this->getDisorderSections_V1($element->patient_type) as $disorder_section) {
        ?>
        <div class="collapse-data-header-icon collapse"><h3><?php echo \CHtml::encode($disorder_section->name); ?></h3></div>
        <div class="row data-row">
            <div class="large-12 column end">
                <div class="row">
                    <div class="element-eye column">
                        <?php $this->renderPartial('view_Element_OphCoCvi_ClinicalInfo_Disorder_Assignment_Disorders_Side_V1', array(
                            'element' => $element,
                            'disorder_section' => $disorder_section,
                        )) ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
}
?>
