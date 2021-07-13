<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

use OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Diagnosis_Not_Covered;
use OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo_V1;

$visual_acuity_list = array();
if (isset($element->best_corrected_right_va_list)) {
    if ($element->best_corrected_right_va_list == Element_OphCoCvi_ClinicalInfo_V1::VISUAL_ACUITY_TYPE_SNELLEN) {
        $visual_acuity_list = $element->getSnellenDatas();
    } else if ($element->best_corrected_right_va_list == Element_OphCoCvi_ClinicalInfo_V1::VISUAL_ACUITY_TYPE_LOGMAR) {
        $visual_acuity_list = $element->getLogmarDatas();
    }
}
$modelName = CHtml::modelName($element);
//$icon_path = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.assets.img')."/icon_info.png");
?>
<?php
if ($this->checkClinicalEditAccess()) { ?>

    <div class="element-fields row" id="disorder_sections">
    <?php echo $form->datePicker(
        $element,
        'examination_date',
        array('maxDate' => 'today'),
        array('style' => 'width: 110px;')
    ) ?>
    <div class="indent-correct row">
        <div class="column large-12">
            <div class="row field-row">
                <div class="column large-6">
                    <label><?= CHtml::encode($element->getAttributeLabel('is_considered_blind')); ?></label>
                </div>
                <div class="column large-4">
                    <?php echo $form->radioButtons(
                        $element,
                        'is_considered_blind',
                        array(
                            0 => $element::$NOT_BLIND_STATUS,
                            1 => $element::$BLIND_STATUS,
                        ),
                        $element->is_considered_blind,
                        false,
                        false,
                        false,
                        false,
                        array('nowrapper' => true)
                    ); ?>
                </div>
                <div class="column large-1 end">
                    <p style="margin-top: 6px">
                        <span id="<?=CHtml::encode($modelName)?>_clinical_info_tooltip" class="help">
                            <i class="js-has-tooltip oe-i info small pad right" data-tooltip-content="<strong>SSI - Who should be certified as severely sight impaired?</strong> <br>
        <u>Group 1:</u> Offer to certify as severely sight impaired: people who have visual acuity worse
        than 3/60 Snellen (or equivalent);<br>
        <u>Group 2:</u> Offer to certify as severely sight impaired: people who are 3/60 Snellen or better
        (or equivalent) but worse than 6/60 Snellen (or equivalent) who also have contraction of
        their visual field;<br>
        <u>Group 3:</u> Offer to certify as severely sight impaired: people who are 6/60 Snellen or better
        (or equivalent) who have a clinically significant contracted field of vision which is
        functionally impairing the person e.g. significant reduction of inferior field or bi-temporal hemianopia<br><br>
        <strong>SI - Who should be certified as sight impaired?</strong><br>
        People can be classified into three groups:<br>
        <u>Group 1:</u> Offer to certify as sight impaired: people who are 3/60 to 6/60 Snellen (or
        equivalent) with full field;<br>
        <u>Group 2:</u> Offer to certify as sight impaired: people between 6/60 and 6/24 Snellen (or
        equivalent) with moderate contraction of the field e.g. superior or patchy loss, media
        opacities or aphakia;<br>
        <u>Group 3:</u> Offer to certify as sight impaired: people who are 6/18 Snellen (or equivalent) or
        even better if they have a marked field defect e.g. homonymous hemianopia."></i>
                        </span>
                    </p>
                </div>
            </div>
        </div>
        <div class="column large-12">
            <div class="row field-row">
                <div class="column large-6">
                    <label><?= CHtml::encode($element->getAttributeLabel('information_booklet')); ?></label>
                </div>
                <div class="column large-6">
                    <?php echo $form->radioButtons(
                        $element,
                        'information_booklet',
                        $element->getInformationBooklets(),
                        $element->information_booklet,
                        false,
                        false,
                        false,
                        false,
                        array('nowrapper' => true)
                    ); ?>
                </div>
            </div>
        </div>
        <div class="column large-12">
            <div class="row field-row">
                <div class="column large-4">
                    <label><?= CHtml::encode($element->getAttributeLabel('eclo')); ?></label>
                </div>
                <div class="column large-8">
                    <?php echo $form->radioButtons(
                        $element,
                        'eclo',
                        $element->getEclo(),
                        $element->eclo,
                        false,
                        false,
                        false,
                        false,
                        array('nowrapper' => true)
                    ); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="column large-3 end">
            <h3 class="inline-header">Visual Acuity</h3>
        </div>
    </div>
    <div class="indent-correct">
        <div class="element-eye">
            <div class="active-form">
                <div class="row field-row">
                    <div class="large-4 column end">
                        <?php echo $form->dropDownList(
                            $element,
                            'best_corrected_right_va_list',
                            $element->getBestCorrectedVAList(),
                            array('empty' => '- Please select -'),
                            false
                        ) ?>
                        <input type="hidden" name="<?=CHtml::modelName($element)?>[best_corrected_left_va_list]" id="best_corrected_left_va_list">
                        <input type="hidden" name="<?=CHtml::modelName($element)?>[best_corrected_binocular_va_list]" id="best_corrected_binocular_va_list">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="indent-correct element-eyes sub-element-fields">
        <div class="element-eye right-eye column left side" data-side="right">
            <div class="active-form">
                <div class="row field-row">
                    <div class="large-6 column">
                        <label for="<?php echo 'best_corrected_right_va'; ?>">
                            <?php echo CHtml::encode($element->getAttributeLabel('best_corrected_right_va')); ?>:
                        </label>
                    </div>
                    <div class="large-4 column end">
                        <?php echo $form->dropDownList(
                            $element,
                            'best_corrected_right_va',
                            $visual_acuity_list,
                            array('empty' => '- Please select -', 'nolabel' => true),
                            false
                        ) ?>
                    </div>
                </div>
            </div>
            <div class="active-form">
                <div class="row field-row">
                    <div class="large-6 column">
                        <label for="<?php echo 'best_recorded_right_va'; ?>">
                            <?php echo CHtml::encode($element->getAttributeLabel('best_recorded_right_va')); ?>:
                        </label>
                    </div>
                    <div class="column large-2 end">
                        <?php echo CHtml::checkBox(CHtml::modelName($element).'[best_recorded_right_va]', $element->best_recorded_right_va); ?>
                    </div>
                </div>
            </div>
        </div>


        <div class="element-eye left-eye column right side" data-side="left">
            <div class="active-form">
                <div class="row field-row">
                    <div class="large-4 column">
                        <label for="<?php echo 'best_corrected_left_va'; ?>">
                            <?php echo CHtml::encode($element->getAttributeLabel('best_corrected_left_va')); ?>:
                        </label>
                    </div>
                    <div class="large-4 column end">
                        <?php echo $form->dropDownList(
                            $element,
                            'best_corrected_left_va',
                            $visual_acuity_list,
                            array('empty' => '- Please select -', 'nolabel' => true),
                            false
                        ) ?>
                    </div>
                </div>
            </div>
            <div class="active-form">
                <div class="row field-row">
                    <div class="large-4 column">
                        <label for="<?php echo 'best_recorded_left_va'; ?>">
                            <?php echo CHtml::encode($element->getAttributeLabel('best_recorded_left_va')); ?>:
                        </label>
                    </div>
                    <div class="column large-2 end">
                        <?php echo CHtml::checkBox(CHtml::modelName($element).'[best_recorded_left_va]', $element->best_recorded_left_va); ?>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="indent-correct row">

        <div class="right-eye column side" data-side="right">
            <div class="active-form">
                <div class="row field-row">
                    <div class="large-5 column">
                        <label for="<?php echo 'best_corrected_binocular_va'; ?>">
                            <?php echo CHtml::encode($element->getAttributeLabel('best_corrected_binocular_va')); ?>:
                        </label>
                    </div>
                    <div class="large-2 column end">
                        <?php echo $form->dropDownList(
                            $element,
                            'best_corrected_binocular_va',
                            $visual_acuity_list,
                            array('empty' => '- Please select -', 'nolabel' => true),
                            false
                        ) ?>
                    </div>
                </div>
            </div>
            <div class="active-form">
                <div class="row field-row">
                    <div class="large-5 column">
                        <label for="<?php echo 'best_recorded_binocular_va'; ?>">
                            <?php echo CHtml::encode($element->getAttributeLabel('best_recorded_binocular_va')); ?>:
                        </label>
                    </div>
                    <div class="column large-2 end">
                        <?php echo CHtml::checkBox(CHtml::modelName($element).'[best_recorded_binocular_va]', $element->best_recorded_binocular_va); ?>
                    </div>
                </div>
            </div>
        </div>
    <div class="column large-12">
        <div class="active-form">
            <div class="row field-row">
                <div class="large-6 column">
                    <label for="<?php echo 'field_of_vision'; ?>">
                        <?php echo CHtml::encode($element->getAttributeLabel('field_of_vision')); ?>:
                    </label>
                </div>
                <div class="column large-6 vision-column-vertical-center-form">
                    <?php echo $form->radioButtons(
                        $element,
                        'field_of_vision',
                        $element->getFieldOfVision(),
                        $element->field_of_vision,
                        false,
                        false,
                        false,
                        false,
                        array('nowrapper' => true)
                    ); ?>
                </div>
            </div>
        </div>
        <div class="active-form">
            <div class="row field-row">
                <div class="large-6 column">
                    <label for="<?php echo 'low_vision_service'; ?>">
                        <?php echo CHtml::encode($element->getAttributeLabel('low_vision_service')); ?>:
                    </label>
                </div>
                <div class="column large-6 vision-column-vertical-center-form">
                    <?php echo $form->radioButtons(
                        $element,
                        'low_vision_service',
                        $element->getLowVisionService(),
                        $element->low_vision_service,
                        false,
                        false,
                        false,
                        false,
                        array('nowrapper' => true)
                    ); ?>
                </div>
            </div>
        </div>
    </div>
    </div>

    <div>

        <?php $this->renderPartial('form_Element_OphCoCvi_ClinicalInfo_Disorder_Assignment_Disorders_V1', array(
            'element' => $element,
            'form' => $form,

        )) ?>

        <?php if (isset($element->diagnosis_not_covered)) : ?>
            <hr>
            <div class="column large-12">
                <div class="row">
                    <div class="column large-12 end">
                        <h3 class="inline-header">Diagnosis not covered in any of the above, specify, including ICD 10 code if known and indicating eye or eyes</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="column large-4">
                        <h3>Diagnosis</h3>
                    </div>
                    <div class="column large-1 text-center">
                        <h3>Main cause</h3>
                    </div>
                    <div class="column large-2 text-center">
                        <h3>ICD 10 Code</h3>
                    </div>
                    <div class="column large-1 text-center">
                        <h3>Right eye</h3>
                    </div>
                    <div class="column large-1 text-center">
                        <h3>Left eye</h3>
                    </div>
                    <div class="column large-1 text-center">
                        <h3>Both eyes</h3>
                    </div>
                    <div class="column large-1"></div>
                </div>
                <div class="row field-row">
                    <div class="column large-4">
                        <?php
                        $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                            'name' => 'autocomplete_disorder_id',
                            'id' => 'autocomplete_disorder_id',
                            'sourceUrl' => array('default/cilinicalDiagnosisAutocomplete'),
                            'options' => array(
                                'minLength' => '2',
                                'select' => "js:function(event, ui) {
                                $('#disorder_id').val(ui.item.id);
                                }",
                            ),
                            'htmlOptions' => array('placeholder' => 'Search'),
                        )); ?>
                        <input type="hidden" id="disorder_id" name="disorder_id">
                        <input type="hidden" id="element_id" name="element_id" value="<?= CHtml::encode($element->id) ?>">
                        <input type="hidden" id="disorder_type" name="disorder_type" value="2">
                    </div>
                    <div class="column large-1 text-center">
                        <?php echo CHtml::checkBox("main_cause"); ?>
                    </div>
                    <div class="column large-2  text-center">
                        <input size="5" nowrapper="1" autocomplete="off" type="text" value="" name="icd10" id="icd10" placeholder="ICD 10">
                    </div>
                    <div class="column large-1 text-center">
                        <div class="eyes-radio-validate">
                        <?php
                        echo CHtml::radioButton('eyes', false, array('id' => 'left_eye', 'value' => 2))
                        ?>
                        </div>
                    </div>
                    <div class="column large-1 text-center">
                        <div class="eyes-radio-validate">
                        <?php
                        echo CHtml::radioButton('eyes', false, array('id' => 'right_eye', 'value' => 1))
                        ?>
                        </div>
                    </div>
                    <div class="column large-1 text-center">
                        <div class="eyes-radio-validate">
                        <?php
                        echo CHtml::radioButton('eyes', false, array('id' => 'both_eye', 'value' => 3))
                        ?>
                        </div>
                    </div>
                    <div class="column large-1 text-center">
                        <button id="js-add-diagnosis-not-covered" class="button secondary small">Add</button>
                    </div>
                    <div class="column large-1 text-center">
                        <button class="button button-icon small disabled" id="js-clear-diagnosis-not-covered" title="Delete Diagnosis">
                            <span class="icon-button-small-mini-cross"></span>
                            <span class="hide-offscreen">Remove element</span>
                        </button>
                    </div>
                </div>
                <table class="grid" id="diagnosis_not_covered_table">
                    <tbody>
                    <tr data-id="1"></tr>
                    <?php
                    foreach ($element->diagnosis_not_covered as $diagnosis) {
                        if (isset($diagnosis->disorder) || isset($diagnosis->clinicinfo_disorder)) {
                            switch ($diagnosis->eye_id) {
                                case 1:
                                    $eye = 'Left';
                                    break;
                                case 2:
                                    $eye = 'Right';
                                    break;
                                case 3:
                                    $eye = 'Bilateral';
                                    break;
                            }
                            if ($diagnosis->disorder_type == OphCoCvi_ClinicalInfo_Diagnosis_Not_Covered::TYPE_CLINICINFO_DISORDER) {
                                $disorder_name = $diagnosis->clinicinfo_disorder->term_to_display;
                                $disorder_code = $diagnosis->clinicinfo_disorder->code;
                            } else {
                                $disorder_name = $diagnosis->disorder->term;
                                $disorder_code = $diagnosis->code;
                            }
                            ?>
                            <tr id="diagnosis_not_covered_<?= CHtml::encode($diagnosis->disorder_id) ?>">
                                <td>
                                    <?php echo CHtml::encode($eye) ?>
                                    <?php echo CHtml::encode($disorder_name) ?>
                                    <?php echo $diagnosis->main_cause == 1 ? '(main cause)' : '' ?> -
                                    <?php echo CHtml::encode($disorder_code) ?>
                                </td>
                                <td>
                                    <button class="button button-icon small js-remove-diagnosis-not-covered-element disabled" data-id="<?= CHtml::encode($diagnosis->disorder_id) ?>" title="Delete Diagnosis">
                                        <span class="icon-button-small-mini-cross"></span>
                                        <span class="hide-offscreen">Remove element</span>
                                    </button>
                                </td>
                                <input type="hidden" name="<?= CHtml::modelName($element) ?>[diagnosis_not_covered][<?= CHtml::encode($diagnosis->disorder_id) ?>][disorder_id]" value="<?= CHtml::encode($diagnosis->disorder_id) ?>">
                                <input type="hidden" name="<?= CHtml::modelName($element) ?>[diagnosis_not_covered][<?= CHtml::encode($diagnosis->disorder_id) ?>][main_cause]" value="<?= CHtml::encode($diagnosis->main_cause) ?>">
                                <input type="hidden" name="<?= CHtml::modelName($element) ?>[diagnosis_not_covered][<?= CHtml::encode($diagnosis->disorder_id) ?>][code]" value="<?= CHtml::encode($disorder_code) ?>">
                                <input type="hidden" name="<?= CHtml::modelName($element) ?>[diagnosis_not_covered][<?= CHtml::encode($diagnosis->disorder_id) ?>][eyes]" value="<?= CHtml::encode($diagnosis->eye_id) ?>">
                                <input type="hidden" name="<?= CHtml::modelName($element) ?>[diagnosis_not_covered][<?= CHtml::encode($diagnosis->disorder_id) ?>][disorder_type]" value="<?= CHtml::encode($diagnosis->disorder_type) ?>">
                            </tr>
                        <?php } ?>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script type="text/javascript">
        $(function () {

            var clinical_info_tooltip = new OpenEyes.UI.Tooltip({
                className: 'clinical_info_tooltip tooltip',
            });

            var $icon = $("#<?=CHtml::encode($modelName)?>_clinical_info_tooltip");

            clinical_info_tooltip.setContent($icon.attr("data-tooltip-content"));

            $icon.on('mouseover', function() {
                var offsets = $(this).offset();
                clinical_info_tooltip.show(offsets.left + 20, offsets.top);
            }).mouseout(function (e) {
                clinical_info_tooltip.hide();
            });
        });

    </script>
<?php } else {
    $this->renderPartial('view_Element_OphCoCvi_ClinicalInfo_V1', array('element' => $element));
} ?>