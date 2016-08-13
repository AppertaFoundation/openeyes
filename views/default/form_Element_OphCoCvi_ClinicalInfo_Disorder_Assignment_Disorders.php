<?php
        foreach(OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Disorder_Section::model()
                    ->findAll('`active` = ?',array(1)) as $disorder_section) { ?>
            <?php echo CHtml::hiddenField("ophcocvi_clinicinfo_disorder_section_id[".$disorder_section->id."]" , $disorder_section->id, array('id' => 'hiddenInput')); ?>
            <fieldset class="row field-row">
                    <legend class="large-12 column">
                        <?php echo $disorder_section->name; ?>
                    </legend>
                </fieldset>
            <div class="sub-element-fields element-eyes row">
                <div class="element-eye right-eye column left side" data-side="right">
                    <div class="active-form">
                        <a href="#" class="icon-remove-side remove-side">Remove side</a>
                        <?php $this->renderPartial('form_Element_OphCoCvi_ClinicalInfo_Disorder_Assignment_Disorders_Side', array(
                            'side' => 'right',
                            'element' => $element,
                            'form' => $form,
                            'disorder_section' => $disorder_section,
                        ))?>
                    </div>
                    <div class="inactive-form">
                        <div class="add-side">
                            <a href="#">
                                Add right side <span class="icon-add-side"></span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="element-eye left-eye column right side data-side="left">
                <div class="active-form">
                    <a href="#" class="icon-remove-side remove-side">Remove side</a>
                    <?php $this->renderPartial('form_Element_OphCoCvi_ClinicalInfo_Disorder_Assignment_Disorders_Side', array(
                        'side' => 'left',
                        'element' => $element,
                        'form' => $form,
                        'disorder_section' => $disorder_section,
                    ))?>
                </div>
                <div class="inactive-form">
                    <div class="add-side">
                        <a href="#">
                            Add left side <span class="icon-add-side"></span>
                        </a>
                    </div>
                </div>
            </div>
            </div>
            <?php
                $comments = OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo_Disorder_Section_Comments::model()->getDisorderSectionComments($disorder_section->id,$element->id);
                if($disorder_section->comments_allowed == 1){?>
                <fieldset class="row field-row">
                    <legend class="large-2 column">
                        <?php echo $disorder_section->comments_label; ?>
                    </legend>
                    <div class="large-10 column">
                        <?php echo  CHtml::textArea( "comments_disorder[".$disorder_section->id."]", $comments, array('rows'=>3, 'cols'=>75));?>
                    </div>
                </fieldset>
            <?php }
        }
        ?>