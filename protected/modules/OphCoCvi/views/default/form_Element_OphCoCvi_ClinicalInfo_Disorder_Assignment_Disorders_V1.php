<?php

use OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo_V1;

?>

<div class="row">
    <?php
    if ($this->event->isNewRecord) {
        if ($this->getGetPatientAge() == 17 && $this->getGetPatientMonthDiff() <= 2) : ?>
            <div class="alert-box error with-icon">
                <p>This patient is 2 months away from his/her 18th birthday. The suggested children diagnosis list might
                    need to be changed.</p>
            </div>
        <?php endif; ?>
    <?php } else {
        if ($this->getGetPatientAge() == 17 && $this->getGetPatientMonthDiff() <= 2 && $element->patient_type == Element_OphCoCvi_ClinicalInfo_V1::CVI_TYPE_CHILD) : ?>
            <div class="alert-box error with-icon">
                <p>This patient is 2 months away from his/her 18th birthday. The suggested children diagnosis list might
                    need to be changed.</p>
            </div>
        <?php endif; ?>
    <?php } ?>
    <hr class="divider">
    <div class="flex-layout row">
        <div class="priority-text">Diagnosis list</div>
    </div>

    <table class="cols-12 last-left">
        <colgroup>
            <col class="cols-7">
            <col class="cols-5">
        </colgroup>
        <tbody>
        <tr>
            <td>
                <?php echo $form->radioButtons($element, 'patient_type', $element->getPatientTypes(),
                    $element->patient_type,
                    false, false, false, false,
                    array('nowrapper' => true)
                ); ?>
            </td>
            <td>
                <label class="inline highlight ">
                    <input type="checkbox" name="show_icd10_code" id="js-show_icd10_code" checked> Show ICD 10 Code
                </label>

            </td>
        </tr>
        </tbody>
    </table>
</div>

<?php foreach ($this->getDisorderSections_V1($element->patient_type) as $disorder_section) :?>
    <?php $is_open = $element->hasAffectedCviDisorderInSection($disorder_section);?>

    <div class="collapse-group highlight">
        <div class="header-icon collapse" data-bjc="20"><?=\CHtml::encode($disorder_section->name); ?></div>
        <div class="collapse-group-content " style="display: block;">
            <!-- Unique layout: use VIEW mode layout here! -->
            <div class="element-eyes">
                <div>
                    <table class="cols-full" style="width: 700px;">
                        <colgroup>
                            <col class="cols-7">
                            <col class="cols-1">
                            <col class="cols-4">
                        </colgroup>
                        <tbody>
                        <?php $this->renderPartial('form_Element_OphCoCvi_ClinicalInfo_Disorder_Assignment_Disorders_Side_V1', array(
                            'element' => $element,
                            'form' => $form,
                            'disorder_section' => $disorder_section,
                        )) ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php endforeach;?>
</div>