<div class="row">
    <div class="column large-3 end"><h3 class="inline-header">Disorders</h3></div>
</div>
<?php
        foreach(OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Disorder_Section::model()->getAllDisorderSections($element) as $disorder_section) { ?>
            <hr />
            <?php echo CHtml::hiddenField("ophcocvi_clinicinfo_disorder_section_id[".$disorder_section['disorder']->id."]" , $disorder_section['disorder']->id, array('id' => 'hiddenInput')); ?>
            <fieldset class="row">
                    <legend class="large-12 column">
                        <?php echo $disorder_section['disorder']->name; ?>
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
                    ));?>
                </div>
            </div>

            <?php
                if($disorder_section['disorder']->comments_allowed == 1)
                { ?>
                <fieldset class="row field-row">
                    <div class="large-4 column text-right">
                        <label><?php echo $disorder_section['disorder']->comments_label; ?></label>
                    </div>
                    <div class="large-7 column large-push-1 end">
                        <?php echo  CHtml::textArea( "comments_disorder[".$disorder_section['disorder']->id."]", $disorder_section['comment'], array('rows'=>2, 'cols'=>40));?>
                    </div>
                </fieldset>
            <?php }
        }
        ?>