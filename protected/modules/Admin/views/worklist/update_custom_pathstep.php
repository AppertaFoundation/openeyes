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
            <?= \CHtml::activeDropDownList($model, 'default_state',
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
            <?= $model->getAttributeLabel('standard_pathway_step_type') ?>
        </td>
        <td>
            <?= \CHtml::activeDropDownList($preset_model, 'standard_pathway_step_type_id',
                CHtml::listData(PathwayStepType::getStandardTypes(), 'id', 'long_name'),
                ['class' => 'cols-6 js-standard-pathway-step', 'empty' => 'Select']
            ) ?>
        </td>
    </tr>
    <tr style="<?= !$preset_model->subspecialty_id ? 'display: none;' : '' ?>">
        <td>
            <?= $model->getAttributeLabel('subspecialty_id') ?>
        </td>
        <td>
            <?= \CHtml::activeDropDownList($preset_model, 'subspecialty_id',
                Subspecialty::model()->getList(),
                ['class' => 'cols-6 js-examination-subspecialty', 'empty' => 'None']
            ) ?>
        </td>
    </tr>
    <tr style="<?= !$preset_model->firm_id ? 'display: none;' : '' ?>">
        <td>
            <?= $model->getAttributeLabel('firm_id') ?>
        </td>
        <td>
            <?= \CHtml::activeDropDownList($preset_model, 'firm_id',
                [],
                ['class' => 'cols-6 js-examination-firm', 'empty' => 'None']
            ) ?>
        </td>
    </tr>
    <tr style="<?= !$preset_model->standard_pathway_step_type_id ? 'display: none;' : '' ?>">
        <td>
            <?= $model->getAttributeLabel('preset_model_name') ?>
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
        $('.js-standard-pathway-step').on('change', function () {
            let standardPathway = $('.js-standard-pathway-step :selected').text();
            switch (standardPathway) {
                case 'Examination':
                    setPresetDropdownOptions([], 'Examination Workstep');
                    $('.js-examination-firm').closest('tr').show();
                    $('.js-examination-subspecialty').closest('tr').show();
                    break;
                case 'Letter':
                    setPresetDropdownOptions(<?= $letter_macros ?>, 'Letter Macros');
                    break;
                case 'Drug Administration Preset Order':
                    setPresetDropdownOptions(<?= $pgd_sets ?>, 'PGD Preset Order');
                    break;
                default:
                    $('.js-preset-id').closest('tr').hide();
            }
        });
        $('.js-examination-subspecialty').on('change', function () {
            let subspecialty_id = $(this).val();
            let $firm = $('.js-examination-firm');
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
                    $firm.addClass('js-examination-firm cols-6');
                }
            });
        });
        $('.js-examination-firm').on('change', function () {
            setExaminationWorkflow();
        });
    });

    function setExaminationWorkflow() {
        let workFlowStepsList = <?= $examination_workflow_steps ?>;
        let selectedFirm = $('.js-examination-firm').val();
        let modelNameSelector = $('.js-preset-id');
        modelNameSelector.empty();
        $.each(workFlowStepsList[selectedFirm], function(key, step) {
            modelNameSelector.append($('<option></option>').attr('value', step.id).text(step.name));
        });
    }

    function setPresetDropdownOptions(options, label) {
        let modelNameSelector = $('.js-preset-id');
        modelNameSelector.empty();
        $.each(options, function (key, value) {
            modelNameSelector.append($('<option></option>').attr('value', value).text(key));
        });
        modelNameSelector.parent().prev().text(label);
        $('.js-preset-id').closest('tr').show();
        $('.js-examination-firm').closest('tr').hide();
        $('.js-examination-subspecialty').closest('tr').hide();
    }
</script>