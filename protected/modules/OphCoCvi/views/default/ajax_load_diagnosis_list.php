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
foreach ($disorder_sections as $disorder_section) {
    ?>
    <hr/>
    <section class="js-toggle-container">

        <div class="row">
            <div class="large-11 column disorder-toggle js-toggle">
                <h4><?= CHtml::encode($disorder_section->name); ?></h4>
                <a href="#" class="toggle-trigger toggle-show">
                    <span class="icon-showhide">
                        Show/hide this section
                    </span>
                </a>
            </div>
        </div>
        <div class="js-toggle-body" style="display:none">
            <?php if ($disorder_section->disorders) { ?>
                <div class="sub-element-fields element-eyes row">
                    <div class="element-eye column left side" data-side="right">
                        <?php $this->renderPartial('form_Element_OphCoCvi_ClinicalInfo_Disorder_Assignment_Disorders_Side_V1', array(
                            'element' => $element,
                            'disorder_section' => $disorder_section,
                        )) ?>
                    </div>
                </div>
            <?php } ?>

        </div>
    </section>
<?php } ?>

