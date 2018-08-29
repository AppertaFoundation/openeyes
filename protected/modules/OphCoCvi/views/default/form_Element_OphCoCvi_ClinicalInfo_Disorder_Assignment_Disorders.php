
<div class="cols-3">
  <h3 class="element-title">Disorders</h3>
</div>
<div class="element-fields">
<?php foreach ($this->getDisorderSections() as $disorder_section) {
    $is_open = $element->hasAffectedCviDisorderInSection($disorder_section);
    ?>
    <div class="collapse-group highlight" data-collapse="collapsed">
      <div class="collapse-group-icon">
        <i class="oe-i <?=  $is_open ? 'minus' : 'plus' ?>"></i>
      </div>
      <h3 class="collapse-group-header">
          <?= $disorder_section->name; ?>
      </h3>
    <div class="collapse-group-content" style="<?=  $is_open ? 'display:block' : 'display:none' ?>">
    <?php if ($disorder_section->disorders) { ?>
      <div class="element-fields element-eyes data-group">
          <?php foreach(['left' => 'right', 'right' => 'left'] as $page_side => $eye_side){ ?>
        <div class="js-element-eye <?=$eye_side?>-eye <?=$page_side?> side" data-side="<?= $eye_side?>">
          <div class="active-form">
              <?php $this->renderPartial('form_Element_OphCoCvi_ClinicalInfo_Disorder_Assignment_Disorders_Side', array(
                  'side' => $eye_side,
                  'element' => $element,
                  'form' => $form,
                  'disorder_section' => $disorder_section,
                  )) ?>
          </div>
        </div>
        <?php } ?>
      </div>
    <?php } ?>
    <?php
    if ($disorder_section->comments_allowed == 1) { ?>
        <fieldset class="flex-layout data-group">
            <div class="cols-5">
                <label><?php echo $disorder_section->comments_label; ?></label>
            </div>
            <div class="cols-7">
                <?php
                $section_comment = $element->getDisorderSectionComment($disorder_section);
                $comments = $section_comment ? $section_comment->comments : null;
                echo CHtml::textArea(CHtml::modelName($element) . "[cvi_disorder_section][" . $disorder_section->id . "][comments]",
                    $comments, array()); ?>
            </div>
        </fieldset>
    <?php } ?>
    </div>
    </div>
<?php } ?>
</div>
