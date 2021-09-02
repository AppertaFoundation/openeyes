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

?>
<?php
if ($this->checkClinicalEditAccess()) { ?>
    <div class="element-fields row">
        <?php echo $form->datePicker($element, 'examination_date', array('maxDate' => 'today'),
            array('style' => 'width: 110px;')) ?>
        <div class="indent-correct row">
            <div class="column large-6">
                <div class="row field-row">
                    <div class="column large-4">
                        <label><?= CHtml::encode($element->getAttributeLabel('is_considered_blind'));?></label>
                    </div>
                    <div class="column large-8">
                        <?php echo $form->radioButtons($element, 'is_considered_blind', array(
                            0 => $element::$NOT_BLIND_STATUS,
                            1 => $element::$BLIND_STATUS,
                        ),
                            $element->is_considered_blind,
                            false, false, false, false,
                            array('nowrapper' => true)
                        ); ?>
                    </div>
                </div>
            </div>
            <div class="column large-6 end">
                <div class="row field-row">
                    <div class="column large-5">
                        <label><?= CHtml::encode($element->getAttributeLabel('sight_varies_by_light_levels'));?></label>
                    </div>
                    <div class="column large-7 large-pull-1">
                        <?php echo $form->radioBoolean($element, 'sight_varies_by_light_levels', array('nowrapper' => true)) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="indent-correct row">
            <div class="column large-6">
                <?php echo $form->dropDownList($element, 'low_vision_status_id',
                    CHtml::listData(OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_LowVisionStatus::model()->findAll(array('order' => 'display_order asc')),
                        'id', 'name'), array('empty' => '- Please select -'), false, array('label' => 4, 'field' => 6)) ?>
            </div>
            <div class="column large-6 end">
                <?php echo $form->dropDownList($element, 'field_of_vision_id',
                    CHtml::listData(OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_FieldOfVision::model()->findAll(array('order' => 'display_order asc')),
                        'id', 'name'), array('empty' => '- Please select -'), false, array('label' => 4, 'field' => 6)) ?>
            </div>
        </div>

        <div class="row">
            <div class="column large-3 end"><h3 class="inline-header">Visual Acuity</h3></div>
        </div>
        <div class="indent-correct element-eyes sub-element-fields">
            <div class="element-eye right-eye column left side" data-side="right">
                <div class="active-form">
                    <div class="row field-row">
                        <div class="large-4 column">
                            <label for="<?php echo 'unaided_right_va'; ?>">
                                <?php echo CHtml::encode($element->getAttributeLabel('unaided_right_va')); ?>:
                            </label>
                        </div>
                        <div class="large-6 column end">
                            <?php echo $form->textField($element, 'unaided_right_va',
                                array('size' => 5, 'nowrapper' => true)); ?>
                        </div>
                    </div>
                    <div class="row field-row">
                        <div class="large-4 column">
                            <label for="<?php echo 'best_corrected_right_va'; ?>">
                                <?php echo CHtml::encode($element->getAttributeLabel('best_corrected_right_va')); ?>:
                            </label>
                        </div>
                        <div class="large-6 column end">
                            <?php echo $form->textField($element, 'best_corrected_right_va',
                                array('size' => 5, 'nowrapper' => true)); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="element-eye left-eye column right side" data-side="left">
                <div class="active-form">
                    <div class="row field-row">
                        <div class="large-4 column">
                            <label for="<?php echo 'unaided_left_va'; ?>">
                                <?php echo CHtml::encode($element->getAttributeLabel('unaided_left_va')); ?>:
                            </label>
                        </div>
                        <div class="large-6 column end">
                            <?php echo $form->textField($element, 'unaided_left_va',
                                array('size' => 5, 'nowrapper' => true)); ?>
                        </div>
                    </div>
                    <div class="row field-row">
                        <div class="large-4 column">
                            <label for="<?php echo 'best_corrected_left_va'; ?>">
                                <?php echo CHtml::encode($element->getAttributeLabel('best_corrected_left_va')); ?>:
                            </label>
                        </div>
                        <div class="large-6 column end">
                            <?php echo $form->textField($element, 'best_corrected_left_va',
                                array('size' => 5, 'nowrapper' => true)); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div>
        <?php echo $form->textField($element, 'best_corrected_binocular_va', array('size' => '10'), null, array('label' => '3', 'field' => '2 large-push-2')) ?></div>

        <?php $this->renderPartial('form_Element_OphCoCvi_ClinicalInfo_Disorder_Assignment_Disorders', array(
            'element' => $element,
            'form' => $form,

        ))?>


        <?php echo $form->textArea($element, 'diagnoses_not_covered', array('rows' => 2, 'cols' => 80)) ?>

    </div>
<?php } else {
    $this->renderPartial('view_Element_OphCoCvi_ClinicalInfo', array('element' => $element));
} ?>
<script type="application/javascript">
    $(document).ready(function() {
        $("input[name^=main_cause_]").click(function() {
            ($(this).prop('checked') === true) ? $(this).prop('value', 1) : $(this).prop('value', 0);
        });
    });
</script>
