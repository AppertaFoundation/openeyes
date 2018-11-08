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
<?php
/**
 * @var $this PatientController
 * @var $patient Patient
 * @var $form CActiveForm
 * @var $patient_identifiers PatientIdentifier[]
 */

CHtml::$errorContainerTag = 'small';

$nhs_num_statuses = CHtml::listData(NhsNumberVerificationStatus::model()->findAll(), 'id', 'description');
$countries = CHtml::listData(Country::model()->findAll(), 'id', 'name');
$address_type_ids = CHtml::listData(AddressType::model()->findAll(), 'id', 'name');
$gp = new Gp();
$practice = new Practice();
$general_practitioners = CHtml::listData($gp->gpCorrespondences(), 'id', 'correspondenceName');
$practices = CHtml::listData($practice->practiceAddresses(), 'id', 'letterLine');

$gender_models = Gender::model()->findAll();
$genders = CHtml::listData($gender_models, function ($gender_model) {
    return CHtml::encode($gender_model->name)[0];
}, 'name');

$ethnic_groups = CHtml::listData(EthnicGroup::model()->findAll(), 'id', 'name');
?>

<?php $form = $this->beginWidget('CActiveForm', array(
    'id' => 'patient-form',
    // Please note: When you enable ajax validation, make sure the corresponding
    // controller action is handling ajax validation correctly.
    // There is a call to performAjaxValidation() commented in generated controller code.
    // See class documentation of CActiveForm for details on this.
    'enableAjaxValidation' => true,
)); ?>

<div class="oe-full-content oe-new-patient flex-layout flex-top">
  <div class="patient-inputs-column" >
    <!--<?php if ($patient->hasErrors() || $address->hasErrors() || $contact->hasErrors()) { ?>
        <div class="alert-box error">
            <?= $form->errorSummary(array($contact, $patient, $address)) ?>
            <?= $form->errorSummary($patient_identifiers) ?>
        </div>
      <?php } ?>-->

    <table class="standard highlight-rows">
      <tbody>
      <tr>
        <td>
            <?= $form->label($contact, 'title') ?>
          <br/>
            <?= $form->error($contact, 'title') ?>
        </td>
        <td>
            <?= $form->textField($contact, 'title', array('size' => 40, 'maxlength' => 40, 'placeholder' => 'Title')) ?>
        </td>
      </tr>
      <tr>
        <td class="required">
            <?= $form->label($contact, 'first_name') ?>
          <br/>
            <?= $form->error($contact, 'first_name') ?>
        </td>
        <td>
            <?= $form->textField($contact, 'first_name',
                array('size' => 40, 'maxlength' => 40, 'onblur' => "findDuplicates($patient->id);",
                  'placeholder' => 'First name')) ?>
        </td>
      </tr>
      <tr>
        <td class="required">
            <?= $form->label($contact, 'last_name') ?>
          <br/>
            <?= $form->error($contact, 'last_name') ?>
        </td>
        <td>
            <?= $form->textField($contact, 'last_name',
                array('size' => 40, 'maxlength' => 40, 'onblur' => "findDuplicates($patient->id);",
                  'placeholder' => 'Last name')) ?>
        </td>
      </tr>
      <tr>
        <td>
            <?= $form->label($contact, 'maiden_name') ?>
          <br/>
            <?= $form->error($contact, 'maiden_name') ?>
        </td>
        <td>
            <?= $form->textField($contact, 'maiden_name',
                array('size' => 40, 'maxlength' => 40, 'placeholder' => 'Maiden name')) ?>
        </td>
      </tr>
      <tr class="patient-duplicate-check">
        <td class="required">
            <?= $form->label($patient, 'dob') ?>
          <br/>
            <?= $form->error($patient, 'dob') ?>
        </td>
        <td style="text-align: left;">
            <?php
            if ((bool)strtotime($patient->dob)) {
                $dob = new DateTime($patient->dob);
                $patient->dob = $dob->format('d/m/Y');
            } else {
                $patient->dob = str_replace('-', '/', $patient->dob);
            }
            ?>
            <?= $form->textField($patient, 'dob', array('onblur' => "findDuplicates($patient->id);",
              'placeholder' => 'dd/mm/yyyy', 'class' => 'date')) ?>
            <?php /*$this->widget('zii.widgets.jui.CJuiDatePicker', array(
                'name' => 'Patient[dob]',
                'id' => 'patient_dob',
                'options' => array(
                    'showAnim' => 'fold',
                    'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                ),
                'value' => $patient->NHSDate('dob', $patient->dob),
                'htmlOptions' => array(
                    'class' => 'small fixed-width',
                ),
            ))*/ ?>
        </td>
      </tr>
      <tr>
        <td class="required">
            <?= $form->label($patient, 'gender') ?>
          <br/>
            <?= $form->error($patient, 'gender') ?>
        </td>
        <td>
            <?= $form->dropDownList($patient, 'gender', $genders, array('empty' => '-- select --')) ?>
        </td>
      </tr>
      <tr>
        <td>
            <?= $form->label($patient, 'ethnic_group_id') ?>
          <br/>
            <?= $form->error($patient, 'ethnic_group_id') ?>
        </td>
        <td>
            <?= $form->dropDownList($patient, 'ethnic_group_id', $ethnic_groups,
                array('empty' => '-- select --')); ?>
        </td>
      </tr>
      <tr>
        <td>
            <?php $this->renderPartial('_form_address', array(
                'form' => $form,
                'address' => $address,
                'countries' => $countries,
                'address_type_ids' => $address_type_ids,
            )); ?>
        </td>
      </tr>
      <tr>
        <td>
            <?= $form->label($contact, 'primary_phone') ?>
          <br/>
            <?= $form->error($contact, 'primary_phone') ?>
        </td>
        <td>
            <?= $form->telField($contact, 'primary_phone', array('size' => 15, 'maxlength' => 20)) ?>
        </td>
      </tr>
      <tr>
        <td>
            <?= $form->label($address, 'email') ?>
          <br/>
            <?= $form->error($address, 'email') ?>
        </td>
        <td>
            <?= $form->emailField($address, 'email', array('size' => 15, 'maxlength' => 255)) ?>
        </td>
      </tr>
      </tbody>
    </table>
  </div>

  <div class="patient-inputs-column">
    <div class="row divider">
      <table class="standard highlight-rows">
        <tbody>
        <tr>
          <td>
              <?= $form->label($patient, 'hos_num') ?>
            <br/>
              <?= $form->error($patient, 'hos_num') ?>
          </td>
          <td>
              <?= $form->textField($patient, 'hos_num',
                  array('size' => 40, 'maxlength' => 40, 'placeholder' => $patient->getAttributeLabel('hos_num'))) ?>
          </td>
        </tr>
        <tr>
          <td>
            <?= Yii::app()->params['nhs_num_label']?> Number
          </td>
          <td>
              <?= $form->textField($patient, 'nhs_num',
                  array(
                      'size' => 40,
                      'maxlength' => 40,
                      'data-child_row' => '.nhs-num-status',
                      'placeholder' => $patient->getAttributeLabel('nhs_num'),
                  )); ?>
              <?= $form->error($patient, 'nhs_num') ?>
          </td>
        </tr>
        <tr style="<?= !$patient->nhs_num ? 'display: none;' : '' ?>">
          <td>
              <?= $form->label($patient, 'nhs_num_status_id') ?>
            <br/>
              <?= $form->error($patient, 'nhs_num_status_id') ?>
          </td>
          <td>
              <?= $form->dropDownList($patient, 'nhs_num_status_id', $nhs_num_statuses,
                  array('empty' => '-- select --')); ?>
          </td>
        </tr>
        <?= $this->renderPartial('crud/_patient_identifiers', array(
                'form' => $form,
                'patient_identifiers' => $patient_identifiers,
                'patient' => $patient,
            )
        ) ?>
        </tbody>
      </table>
    </div>
    <div class="row divider">
      <table class="standard highlight-rows">
        <tbody>
        <tr>
          <td>
            <label class="inline highlight ">
                <?= $form->checkBox($patient, 'is_deceased', array('data-child_row' => '.date_of_death')) ?>
              is deceased
            </label>
            <br/>
              <?= $form->error($patient, 'is_deceased') ?>
          </td>
          <td>
            <div class="flex-layout date_of_death" style="<?= $patient->is_deceased == 0 ? 'display: none;' : '' ?>">
              <span><?= $form->label($patient, 'date_of_death') ?></span>
                <?php
                if ((bool)strtotime($patient->date_of_death)) {
                    $date_of_death = new DateTime($patient->date_of_death);
                    $patient->date_of_death = $date_of_death->format('d/m/Y');
                } else {
                    $patient->date_of_death = str_replace('-', '/', $patient->date_of_death);
                }
                ?>

                <?= $form->textField($patient, 'date_of_death', array('placeholder' => 'dd/mm/yyy', 'class' => 'date')) ?>

                <?php /*$this->widget('zii.widgets.jui.CJuiDatePicker', array(
                'name' => 'Patient[date_of_death]',
                'id' => 'date_to',
                'options' => array(
                    'showAnim' => 'fold',
                    'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                ),
                'value' => $patient->NHSDate('date_of_death', $patient->date_of_death),
                'htmlOptions' => array(
                    'class' => 'small fixed-width',
                ),
            ))*/ ?>
            </div>
            <br/>
              <?= $form->error($patient, 'date_of_death') ?>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
    <div class="row divider">
      <table class="standard highlight-rows">
        <tbody>
        <tr>
          <td>
              <?= $form->label($patient, 'gp_id') ?>
            <br/>
              <?= $form->error($patient, 'gp_id') ?>
          </td>
          <td>
              <?php $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                  'name' => 'gp_id',
                  'id' => 'autocomplete_gp_id',
                  'source' => "js:function(request, response) {
                                    $.getJSON('/patient/gpList', {
                                            term : request.term
                                    }, response);
                            }",
                  'options' => array(
                      'select' => "js:function(event, ui) {
                                    removeSelectedGP();
                                    addItem('selected_gp_wrapper', ui);
                                    $('#autocomplete_gp_id').val('');
                                    return false;
                    }",
                      'response' => 'js:function(event, ui){
                        if(ui.content.length === 0){
                            $("#no_gp_result").show();
                        } else {
                            $("#no_gp_result").hide();
                        }
                    }',
                  ),
                  'htmlOptions' => array(
                      'placeholder' => 'search GP',
                  ),

              )); ?>


            <div id="selected_gp_wrapper" style="<?= !$patient->gp_id ? 'display: none;' : '' ?>">
              <ul class="oe-multi-select js-selected_gp">
                <li>
                  <span class="js-name">
                      <?= $patient->gp_id ? $patient->gp->CorrespondenceName : '' ?>
                  </span>
                  <i class="oe-i remove-circle small-icon pad-left js-remove-gp"></i>
                </li>
              </ul>
                <?= CHtml::hiddenField('Patient[gp_id]', $patient->gp_id, array('class' => 'hidden_id')) ?>
            </div>
            <div id="no_gp_result" style="display: none;">
              <div>No result</div>
            </div>
          </td>
        </tr>
        <tr>
          <td>
              <?= $form->label($patient, 'practice_id') ?>
            <br/>
              <?= $form->error($patient, 'practice_id') ?>
          </td>
          <td>
              <?php $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                  'name' => 'practice_id',
                  'id' => 'autocomplete_practice_id',
                  'source' => "js:function(request, response) {
                                    $.getJSON('/patient/practiceList', {
                                            term : request.term
                                    }, response);
                            }",
                  'options' => array(
                      'select' => "js:function(event, ui) {
                                    removeSelectedPractice();
                                    addItem('selected_practice_wrapper', ui);
                                    $('#autocomplete_practice_id').val('');
                                    return false;
                    }",
                      'response' => 'js:function(event, ui){
                        if(ui.content.length === 0){
                            $("#no_practice_result").show();
                        } else {
                            $("#no_practice_result").hide();
                        }
                    }',
                  ),
                  'htmlOptions' => array(
                      'placeholder' => 'search Practice',
                  ),

              )); ?>

            <div id="selected_practice_wrapper" style="<?= !$patient->practice_id ? 'display: none;' : '' ?>">
              <ul class="oe-multi-select js-selected_practice">
                <li>
                  <span class="js-name">
                      <?= $patient->practice_id ? $patient->practice->getAddressLines() : '' ?>
                  </span>
                  <i class="oe-i remove-circle small-icon pad-left js-remove-practice"></i>
                </li>
              </ul>

                <?= CHtml::hiddenField('Patient[practice_id]', $patient->practice_id,
                    array('class' => 'hidden_id')); ?>
            </div>
            <div id="no_practice_result" style="display: none;">
              <div>No result</div>
            </div>
          </td>
        </tr>
        </tbody>
      </table>
    </div>

    <div class="row flex-layout flex-right">
        <?= CHtml::submitButton($patient->isNewRecord ? 'Create new patient' : 'Save patient',
            array('class' => 'button green hint')); ?>
    </div>
  </div>
</div>
<?php $this->endWidget(); ?>

<script>

  function findDuplicates(id) {
    var first_name = $('#Contact_first_name').val();
    var last_name = $('#Contact_last_name').val();
    var date_of_birth = $('#Patient_dob').val();
    if (first_name && last_name && date_of_birth) {
      $.ajax({
          url: "<?php echo Yii::app()->controller->createUrl('patient/findDuplicates'); ?>",
          data: {firstName: first_name, last_name: last_name, dob: date_of_birth, id: id},
          type: 'GET',
          success: function (response) {
            $('#conflicts').remove();
            $('.patient-duplicate-check').after(response);
          }
        }
      );
    }
  }

</script>
