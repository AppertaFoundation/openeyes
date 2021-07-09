<div class="row">
    <div class="column large-3 end"><h3 class="inline-header">Disorders</h3></div>
</div>
<div class="row">
    <div class="column large-2 large-push-3"><h3>RIGHT</h3></div>
    <div class="column large-2 large-push-6 end"><h3>LEFT</h3></div>
</div>
<?php
foreach ($this->getDisorderSections() as $disorder_section) {
    $is_open = $element->hasAffectedCviDisorderInSection($disorder_section);
    ?>
    <hr/>
    <section class="js-toggle-container">

        <div class="row">
            <div class="large-11 column disorder-toggle js-toggle">
                <h4><?= CHtml::encode($disorder_section->name); ?></h4>
                <a href="#" style="width:100%; text-align: right;" class="toggle-trigger <?= $is_open ? 'toggle-hide' : 'toggle-show' ?>">
                    <span class="icon-showhide">
                        Show/hide this section
                    </span>
                </a>
            </div>
        </div>
    <div class="js-toggle-body" style="<?=  $is_open ? 'display:block' : 'display:none' ?>">
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
                <label><?php echo CHtml::encode($disorder_section->comments_label); ?></label>
            </div>
            <div class="large-7 column large-push-1 end">
                <?php
                $section_comment = $element->getDisorderSectionComment($disorder_section);
                $comments = $section_comment ? $section_comment->comments : null;
                echo CHtml::textArea(CHtml::modelName($element) . "[cvi_disorder_section][" . $disorder_section->id . "][comments]",
                    $comments, array('rows' => 2, 'cols' => 40)); ?>
            </div>
        </fieldset>
    <?php } ?>
    </div>
    </section>
<?php }