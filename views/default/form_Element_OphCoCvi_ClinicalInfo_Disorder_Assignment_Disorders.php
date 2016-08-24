<div class="row">
    <div class="column large-3 end"><h3 class="inline-header">Disorders</h3></div>
</div>
<?php
foreach ($this->getDisorderSections() as $disorder_section) { ?>
    <hr/>
    <fieldset class="row">
        <legend class="large-12 column">
            <?php echo $disorder_section->name; ?>
        </legend>
    </fieldset>
    <?php if ($disorder_section->disorders) { ?>
        <div class="sub-element-fields element-eyes row">
            <div class="element-eye right-eye column left side" data-side="right">
                <?php $this->renderPartial('form_Element_OphCoCvi_ClinicalInfo_Disorder_Assignment_Disorders_Side', array(
                    'side' => 'right',
                    'element' => $element,
                    'form' => $form,
                    'disorder_section' => $disorder_section,
                )) ?>
            </div>
            <div class="element-eye left-eye column right side" data-side="left">
            <?php $this->renderPartial('form_Element_OphCoCvi_ClinicalInfo_Disorder_Assignment_Disorders_Side', array(
                'side' => 'left',
                'element' => $element,
                'form' => $form,
                'disorder_section' => $disorder_section,
            )); ?>
            </div>
        </div>
    <?php } ?>

    <?php
    if ($disorder_section->comments_allowed == 1) { ?>
        <fieldset class="row field-row">
            <div class="large-4 column text-right">
                <label><?php echo $disorder_section->comments_label; ?></label>
            </div>
            <div class="large-7 column large-push-1 end">
                <?php
                $section_comment = $element->getDisorderSectionComment($disorder_section);
                $comments = $section_comment ? $section_comment->comments : null;
                echo CHtml::textArea("comments_disorder[" . $disorder_section->id . "]",
                    $comments, array('rows' => 2, 'cols' => 40)); ?>
            </div>
        </fieldset>
    <?php }
}
?>