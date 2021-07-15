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
                <div class="right-eye">

                    <table class="cols-full">
                        <colgroup>
                            <col>
                            <col class="cols-6">
                        </colgroup>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php endforeach;?>

<div id="diagnosis_list">
    <div class="row">
        <div class="column large-4"><h3>Diagnosis</h3></div>
        <div class="column large-2 text-center"><h3>Main cause</h3></div>
        <div class="column large-2 text-center icd10code"><h3>ICD 10 Code</h3></div>
        <div class="column large-1 text-center"><h3>Right eye</h3></div>
        <div class="column large-1 text-center"><h3>Left eye</h3></div>
        <div class="column large-1 text-center"><h3>Both eyes</h3></div>
        <div class="column large-1"></div>
    </div>
    <?php
    foreach ($this->getDisorderSections_V1($element->patient_type) as $disorder_section) {
        $is_open = $element->hasAffectedCviDisorderInSection($disorder_section);
        ?>
        <hr/>
        <section class="js-toggle-container">

            <div class="row">
                <div class="large-11 column disorder-toggle js-toggle">
                    <h4><?= CHtml::encode($disorder_section->name); ?></h4>
                    <a href="#" class="toggle-trigger <?= $is_open ? 'toggle-hide' : 'toggle-show' ?>">
                        <span class="icon-showhide">
                            Show/hide this section
                        </span>
                    </a>
                </div>
            </div>
            <div class="js-toggle-body" style="<?= $is_open ? 'display:block' : 'display:none' ?>">
                <?php if ($disorder_section->disorders) { ?>
                    <div class="sub-element-fields element-eyes row">
                        <div class="element-eye column left side" data-side="right">
                            <?php $this->renderPartial('form_Element_OphCoCvi_ClinicalInfo_Disorder_Assignment_Disorders_Side_V1', array(
                                'element' => $element,
                                'form' => $form,
                                'disorder_section' => $disorder_section,
                            )) ?>
                        </div>
                    </div>
                <?php } ?>

            </div>
        </section>
    <?php } ?>
</div>