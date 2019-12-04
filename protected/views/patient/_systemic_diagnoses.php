<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<section class="element view full patient-info associated-data js-toggle-container">
    <header class="element-header">
        <h3 class="element-title">
            <span class="icon-patient-clinician-hd_flag"></span>
            Systemic Diagnoses
        </h3>
    </header>

    <div class="js-toggle-body">

        <table class="plain patient-data">
            <thead>
            <tr>
                <th>Date</th>
                <th>Diagnosis</th>
                <?php if ($this->checkAccess('OprnEditSystemicDiagnosis')) {
                    ?><th>Actions</th><?php
                } ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($this->patient->systemicDiagnoses as $diagnosis) {?>
                <tr>
                    <td><?= $diagnosis->dateText?></td>
                    <td><?= $diagnosis->eye ? $diagnosis->eye->adjective : ''?> <?= $diagnosis->disorder->term?></td>
                    <?php if ($this->checkAccess('OprnEditSystemicDiagnosis')) { ?>
                        <td><a href="#" class="removeDiagnosis" rel="<?= $diagnosis->id?>">Remove</a></td>
                    <?php } ?>
                </tr>
            <?php }?>
            </tbody>
        </table>

        <?php if ($this->checkAccess('OprnEditSystemicDiagnosis')) { ?>
            <div class="box-actions">
                <button id="btn-add_new_systemic_diagnosis" class="secondary small">
                    Add Systemic Diagnosis
                </button>
            </div>

            <div id="add_new_systemic_diagnosis" style="display: none;">

                <?php
                $form = $this->beginWidget('FormLayout', array(
                        'id' => 'add-systemic-diagnosis',
                        'enableAjaxValidation' => false,
                        'action' => array('patient/adddiagnosis'),
                        'layoutColumns' => array(
                            'label' => 3,
                            'field' => 9,
                        ),
                        'htmlOptions' => array(
                            'class' => 'form add-data',
                        ),
                    ))?>

                <fieldset class="data-group">

                    <legend><strong>Add Systemic diagnosis</strong></legend>

                    <?php $form->widget('application.widgets.DiagnosisSelection', array(
                            'field' => 'systemic_disorder_id',
                            'label' => 'Diagnosis',
                            'options' => CommonSystemicDisorder::getList(Firm::model()->findByPk($this->selectedFirmId)),
                            'restrict' => 'systemic',
                            'default' => false,
                            'layout' => 'patientSummary',
                            'loader' => 'add_systemic_diagnosis_loader',
                        ))?>

                    <div class="hide" id="add_systemic_diagnosis_loader">
                        <p class="large-offset-<?= $form->layoutColumns['label'];?> large-<?= $form->layoutColumns['field'];?> column end">
                            <img class="loader" src="<?= Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" />
                                searching...
                        </p>
                    </div>

                    <input type="hidden" name="patient_id" value="<?= $this->patient->id?>" />

                    <fieldset class="diagnosis_eye data-group">
                        <legend class="<?= $form->columns('label');?>">
                            Side:
                        </legend>
                        <div class="<?= $form->columns('field');?>">
                            <label class="inline">
                                <input type="radio" name="diagnosis_eye" class="diagnosis_eye" value="" checked="checked" /> None
                            </label>
                            <?php foreach (Eye::model()->findAll(array('order' => 'display_order')) as $eye) {?>
                                <label class="inline">
                                    <input type="radio" name="diagnosis_eye" class="diagnosis_eye" value="<?= $eye->id?>" /> <?= $eye->name?>
                                </label>
                            <?php }?>
                        </div>
                    </fieldset>

                    <?php $this->renderPartial('_fuzzy_date', array('form' => $form, 'label' => 'Date diagnosed'))?>

                    <div class="systemic_diagnoses_form_errors alert-box alert hide"></div>

                    <div class="buttons">
                        <img src="<?= Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" class="add_systemic_diagnosis_loader hide" />
                        <button type="submit" class="secondary small btn_save_systemic_diagnosis">
                            Save
                        </button>
                        <button class="warning small btn_cancel_systemic_diagnosis">
                            Cancel
                        </button>
                    </div>

                </fieldset>
                <?php $this->endWidget()?>

            </div>
        <?php } ?>
    </div>

</section>
<?php if ($this->checkAccess('OprnEditSystemicDiagnosis')) { ?>
    <script type="text/javascript">
        $('#btn-add_new_systemic_diagnosis').click(function() {
            $('#add_new_systemic_diagnosis').slideToggle('fast');
            $('#btn-add_new_systemic_diagnosis').attr('disabled',true);
            $('#btn-add_new_systemic_diagnosis').addClass('disabled');
        });
        $('button.btn_cancel_systemic_diagnosis').click(function(e) {
            $('#add_new_systemic_diagnosis').slideToggle('fast');
            $('#btn-add_new_systemic_diagnosis').attr('disabled',false);
            $('#btn-add_new_systemic_diagnosis').removeClass('disabled');
            OpenEyes.Form.reset($(e.target).closest('form'));
            return false;
        });
        $('button.btn_save_systemic_diagnosis').click(function() {
            $.ajax({
                'type': 'POST',
                'dataType': 'json',
                'url': baseUrl+'/patient/validateadddiagnosis',
                'data': $('#add-systemic-diagnosis').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
                'success': function(data) {
                    $('div.systemic_diagnoses_form_errors').html('').hide();
                    if (data.length == 0) {
                        $('img.add_systemic_diagnosis_loader').show();
                        $('#add-systemic-diagnosis').submit();
                        return true;
                    } else {
                        for (var i in data) {
                            $('div.systemic_diagnoses_form_errors').show().append('<div>'+data[i]+'</div>');
                        }
                    }
                }
            });
            return false;
        });
    </script>
<?php } ?>
