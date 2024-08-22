<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>

<?php
$visual_field_test_preset_value = null;
$eye_value = null;
if (isset($preset_model->preset_id)) {
    [$visual_field_test_preset_value, $eye_value] = PathwayStepTypePresetAssignment::getVisualFieldsPresetIdAndLaterality($preset_model->preset_id);
}
?>

<div class="row divider">
    <h2><?= $model->id ? 'Edit' : 'Add' ?> Custom Path Step</h2>
</div>

<?php echo $this->renderPartial('//admin/_form_errors', array('errors' => $errors)) ?>
<table class="standard cols-full">
    <?php
    $form = $this->beginWidget(
        'BaseEventTypeCActiveForm',
        [
            'id' => 'adminform',
            'enableAjaxValidation' => false,
            'layoutColumns' => array(
                'label' => 2,
                'field' => 4,
            ),
        ]
    ) ?>
    <tbody>
    <tr>
        <td>
            <?= $model->getAttributeLabel('institution') ?>
        </td>
        <td>
            <?= Institution::model()->getCurrent()->name ?>
        </td>
    </tr>
    <tr>
        <td>
            <?= $model->getAttributeLabel('long_name') ?>
        </td>
        <td>
            <?= \CHtml::activeTextField($model, 'long_name', ['class' => 'cols-6']) ?>
        </td>
    </tr>
    <tr>
        <td>
            <?= $model->getAttributeLabel('short_name') ?>
        </td>
        <td>
            <?= \CHtml::activeTextField($model, 'short_name', ['class' => 'cols-6']) ?>
        </td>
    </tr>
    <tr>
        <td>
            <?= $model->getAttributeLabel('default_state') ?>
        </td>
        <td>
            <?= \CHtml::activeDropDownList(
                $model,
                'default_state',
                [
                    PathwayStep::STEP_REQUESTED => 'To Do',
                    PathwayStep::STEP_STARTED => 'Active',
                    PathwayStep::STEP_COMPLETED => 'Completed',
                    PathwayStep::STEP_DRAFT => 'Draft',
                ],
                ['class' => 'cols-6'],
            ) ?>
        </td>
    </tr>
    <tr>
        <td>
            <?= $model->getAttributeLabel('active') ?>
        </td>
        <td>
            <?= \CHtml::activeCheckBox($model, 'active') ?>
        </td>
    </tr>
    <tr>
        <td>
            <?= $preset_model->getAttributeLabel('standard_pathway_step_type_id') ?>
        </td>
        <td>
            <?php $criteria = new CDbCriteria();
                $criteria->addCondition("`short_name` != 'drug admin'")
            ?>
            <?= \CHtml::activeDropDownList(
                $preset_model,
                'standard_pathway_step_type_id',
                CHtml::listData(PathwayStepType::getStandardTypes($criteria), 'id', 'long_name'),
                ['class' => 'cols-6 js-standard-pathway-step', 'empty' => 'Select']
            ) ?>
        </td>
    </tr>
    <tr style="display: none;">
        <td>
            <?= $preset_model->getAttributeLabel('site_id') ?>
        </td>
        <td>
            <?= CHtml::activeDropDownList(
                $preset_model,
                'site_id',
                Site::model()->getListForCurrentInstitution('name'),
                ['class' => 'cols-6 js-pathstep-site', 'empty' => 'None']
            ) ?>
        </td>
    </tr>
    <tr style="display: none;">
        <td>
            <?= $preset_model->getAttributeLabel('subspecialty_id') ?>
        </td>
        <td>
            <?= \CHtml::activeDropDownList(
                $preset_model,
                'subspecialty_id',
                Subspecialty::model()->getList(),
                ['class' => 'cols-6 js-pathstep-subspecialty', 'empty' => 'None']
            ) ?>
        </td>
    </tr>
    <tr style="display: none;">
        <td>
            <?= $preset_model->getAttributeLabel('firm_id') ?>
        </td>
        <td>
            <?= \CHtml::activeDropDownList(
                $preset_model,
                'firm_id',
                Firm::model()->getList(Yii::app()->session['selected_institution_id'], $preset_model->subspecialty_id),
                ['class' => 'cols-6 js-pathstep-firm', 'empty' => 'None']
            ) ?>
        </td>
    </tr>
    <tr style="display: none;">
        <td>
            <?= $preset_model->getAttributeLabel('preset_model_name') ?>
        </td>
        <td>
            <?= CHtml::dropDownList(
                'PathwayStepTypePresetAssignment[preset_id]',
                $preset_model->preset_id,
                [],
                ['class' => 'cols-6 js-preset-id', 'empty' => 'Select']
            ) ?>
        </td>
    </tr>
    <tr style="display: none;">
        <td>
            <?= $preset_model->getAttributeLabel('booking_period') ?>
        </td>
        <td>
            <?= CHtml::dropDownList(
                'PathwayStepTypePresetAssignment[duration_value]',
                $preset_model->preset_id % 100,
                array_combine(range(1, 18), range(1, 18)),
                ['class' => 'cols-2 js-booking-value', 'empty' => 'Time']
            ) ?>
            <?= CHtml::dropDownList(
                'PathwayStepTypePresetAssignment[duration_period]',
                intdiv($preset_model->preset_id, 100),
                [1 => 'days', 2 => 'weeks', 3 => 'months', 4 => 'years'],
                ['class' => 'cols-4 js-booking-period', 'empty' => 'Period']
            ) ?>
        </td>
    </tr>
    <tr style="display: none;">
        <td>
            <?= $preset_model->getAttributeLabel('visual_field_test_preset') ?>
        </td>
        <td>
            <?= CHtml::dropDownList(
                'PathwayStepTypePresetAssignment[visual_field_test_preset]',
                $visual_field_test_preset_value,
                $visual_field_test_preset,
                ['class' => 'cols-2 js-visual-field-test-preset-value']
            ) ?>
        </td>
    </tr>
    <tr style="display: none;">
        <td>
            <?= $preset_model->getAttributeLabel('laterality') ?>
        </td>
        <td>
            <?= CHtml::dropDownList(
                'PathwayStepTypePresetAssignment[laterality]',
                $eye_value,
                $eye,
                ['class' => 'cols-2 js-laterality-value']
            ) ?>
        </td>
    </tr>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="6">
            <?php echo $form->formActions(array(
                'submit' => 'Save custom pathstep',
                'cancel-uri' => '/Admin/worklist/customPathSteps',
            )) ?>
            <?php $this->endWidget() ?>
        </td>
    </tr>
    </tfoot>
</table>
<script type="text/javascript">
    $(document).ready(function () {
        setPresetOptions();     // Update dropdown list based on standard pathway type selected
        $('.js-pathstep-subspecialty').on('change', function () {
            let subspecialty_id = $(this).val();
            let $firm = $('.js-pathstep-firm');
            $('.js-preset-id').empty();
            $firm.empty();
            $.ajax({
                'type': 'GET',
                'url': baseUrl + '/Firm/getFirmsBySubspecialty?subspecialty_id=' + subspecialty_id,
                dataType: "json",
                'success': function (data) {
                    $firm.append($('<option>', {
                        text: 'None',
                        value: '',
                    }));
                    for (var id in data) {
                        if (data.hasOwnProperty(id)) {
                            $firm.append($('<option>', {
                                value: id,
                                text: data[id]
                            }));
                        }
                    }
                    $firm.addClass('js-pathstep-firm cols-6');
                }
            });
        });
        $('.js-pathstep-firm').on('change', function () {
            setExaminationWorkflow();
        });
        $('.js-standard-pathway-step').on('change', function () {
            setPresetOptions();
        });
    });

    function setExaminationWorkflow() {
        let workFlowStepsList = <?= $examination_workflow_steps ?>;
        let selectedFirm = $('.js-pathstep-firm').val();
        let modelNameSelector = $('.js-preset-id');
        modelNameSelector.empty();
        $.each(workFlowStepsList[selectedFirm], function(key, step) {
            modelNameSelector.append($('<option></option>').attr('value', step.id).attr('selected', step.id === '<?= $preset_model->preset_id ?>').text(step.name));
        });
    }

    function setPresetDropdownOptions(options, label) {
        let modelNameSelector = $('.js-preset-id');
        modelNameSelector.empty();
        $.each(options, function (key, value) {
            modelNameSelector.append($('<option></option>').attr('value', value).attr('selected', value === '<?= $preset_model->preset_id ?>').text(key));
        });
        modelNameSelector.parent().prev().text(label);
        modelNameSelector.closest('tr').show();
        $('.js-booking-value').closest('tr').hide();
        $('.js-pathstep-site').closest('tr').hide();
        $('.js-pathstep-firm').closest('tr').hide();
        $('.js-pathstep-subspecialty').closest('tr').hide();
    }

    function setBookingDropdownOptions() {
        $('.js-pathstep-site').closest('tr').show();
        $('.js-pathstep-firm').closest('tr').show();
        $('.js-pathstep-subspecialty').closest('tr').show();
        $('.js-booking-value').closest('tr').show();
        $('.js-preset-id').closest('tr').hide();
    }


    function setVisualFieldsDropdownOptions() {
        $('.js-visual-field-test-preset-value').closest('tr').show();
        $('.js-laterality-value').closest('tr').show();
        $('.js-preset-id').closest('tr').hide();
    }

    function setPresetOptions()
    {
        let standardPathway = $('.js-standard-pathway-step :selected').text();
        switch (standardPathway) {
            case 'Examination':
                setPresetDropdownOptions([], 'Examination Workstep');
                $('.js-pathstep-firm').closest('tr').show();
                $('.js-pathstep-subspecialty').closest('tr').show();
                if ($('.js-pathstep-firm :selected').val() !== '') {
                    setExaminationWorkflow();
                }
                break;
            case 'Letter':
                setPresetDropdownOptions(<?= $letter_macros ?>, 'Letter Macros');
                break;
            case 'Drug Administration Preset Order':
                setPresetDropdownOptions(<?= $pgd_sets ?>, 'PGD Preset Order');
                break;
            case 'Book Follow-up Appointment':
                setBookingDropdownOptions();
                break;
            case 'Visual Fields':
                setVisualFieldsDropdownOptions();
                break;
            default:
                setPresetDropdownOptions([], 'Default');
                $('.js-preset-id').closest('tr').hide();
        }
    }
</script>
