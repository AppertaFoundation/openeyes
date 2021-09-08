<?php
foreach ($this->getDisorderSections() as $disorder_section) {
    $comments = OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo_Disorder_Section_Comments::model()
        ->getDisorderSectionComments($disorder_section->id, $element->id);
    ?>
        <fieldset class="row field-row">
            <legend class="large-12 column">
                <?php echo CHtml::encode($disorder_section->name); ?>
            </legend>
        </fieldset>
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
        if ($disorder_section->comments_allowed == 1) {
            if ($comments != '') { ?>
                <fieldset class="row field-row">
                    <legend class="large-4 column">
                        <?php echo CHtml::encode($disorder_section->comments_label); ?>
                    </legend>
                    <div class="large-8 column">
                        <?php echo CHtml::encode($comments); ?>
                    </div>
                </fieldset>
            <?php }
        }
}
?>
