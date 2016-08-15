<div class="row">
    <div class="column large-3 end"><h3 class="inline-header">Disorders</h3></div>
</div>
<?php
        foreach(OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Disorder_Section::model()
                    ->findAll('`active` = ?',array(1)) as $disorder_section) { ?>
            <hr />
            <?php echo CHtml::hiddenField("ophcocvi_clinicinfo_disorder_section_id[".$disorder_section->id."]" , $disorder_section->id, array('id' => 'hiddenInput')); ?>
            <fieldset class="row">
                    <legend class="large-12 column">
                        <?php echo $disorder_section->name; ?>
                    </legend>
                </fieldset>
            <div class="sub-element-fields element-eyes row">
                <div class="element-eye right-eye column left side" data-side="right">
                    <?php $this->renderPartial('form_Element_OphCoCvi_ClinicalInfo_Disorder_Assignment_Disorders_Side', array(
                        'side' => 'right',
                        'element' => $element,
                        'form' => $form,
                        'disorder_section' => $disorder_section,
                    ))?>
                </div>
                <div class="element-eye left-eye column right side data-side="left">
                    <?php $this->renderPartial('form_Element_OphCoCvi_ClinicalInfo_Disorder_Assignment_Disorders_Side', array(
                        'side' => 'left',
                        'element' => $element,
                        'form' => $form,
                        'disorder_section' => $disorder_section,
                    ))?>
                </div>
            </div>

            <?php
                $comments = OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo_Disorder_Section_Comments::model()->getDisorderSectionComments($disorder_section->id,$element->id);
                if($disorder_section->comments_allowed == 1){?>
                <fieldset class="row field-row">
                    <div class="large-2 column large-push-2">
                        <label><?php echo $disorder_section->comments_label; ?></label>
                    </div>
                    <div class="large-4 column large-push-2 end">
                        <?php echo  CHtml::textArea( "comments_disorder[".$disorder_section->id."]", $comments, array('rows'=>2, 'cols'=>40));?>
                    </div>
                </fieldset>
            <?php }
        }
        ?>