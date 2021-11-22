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

$ethnic_list =  CHtml::listData(EthnicGroup::model()->findAll(), 'id', 'name');
$ethnic_groups = array();
$ethnic_filters = Yii::app()->params['ethnic_group_filters'];
foreach ($ethnic_list as $key => $item) {
    if (!$ethnic_filters || !in_array($item, $ethnic_filters)) {
        $ethnic_groups[$key] = $item;
    }
}
?>

<?php $form = $this->beginWidget('CActiveForm', array(
    'id' => 'patient-form',
    // Please note: When you enable ajax validation, make sure the corresponding
    // controller action is handling ajax validation correctly.
    // There is a call to performAjaxValidation() commented in generated controller code.
    // See class documentation of CActiveForm for details on this.
    'enableAjaxValidation' => true,
    'htmlOptions' => array('enctype' => 'multipart/form-data'),

)); ?>

<div class="oe-full-content oe-new-patient flex-layout flex-top">
  <div class="patient-inputs-column" >
    <!--<?php if ($patient->hasErrors() || $address->hasErrors() || $contact->hasErrors()) { ?>
        <div class="alert-box error">
            <?= $form->errorSummary(array($contact, $patient, $address, $referral)) ?>
            <?= $form->errorSummary($patient_identifiers) ?>
        </div>
        <?php } ?>-->

    <table class="standard highlight-rows">
      <tbody>
      <tr>
        <td class=<?= Yii::app()->params['add_patient_fields']['title'] === 'mandatory' ? 'required':'' ?>>
            <?= $form->label($contact, 'title') ?>
          <br/>
            <?= $form->error($contact, 'title') ?>
        </td>
        <td>
            <?= $form->textField($contact, 'title', array('size' => 40, 'maxlength' => 40, 'placeholder' => 'Title', 'autocomplete' => Yii::app()->params['html_autocomplete'])) ?>
        </td>
      </tr>
      <tr>
        <td class=<?= Yii::app()->params['add_patient_fields']['first_name'] === 'mandatory' ? 'required':'' ?>>
            <?= $form->label($contact, 'first_name') ?>
          <br/>
            <?= $form->error($contact, 'first_name') ?>
        </td>
        <td>
            <?= $form->textField(
                $contact,
                'first_name',
                array('size' => 40, 'maxlength' => 40, 'onblur' => "findDuplicates($patient->id);",
                'placeholder' => 'First name',
                'autocomplete' => Yii::app()->params['html_autocomplete'])
            ) ?>
        </td>
      </tr>
      <tr>
        <td class=<?= Yii::app()->params['add_patient_fields']['last_name'] === 'mandatory' ? 'required':'' ?>>
            <?= $form->label($contact, 'last_name') ?>
          <br/>
            <?= $form->error($contact, 'last_name') ?>
        </td>
        <td>
            <?= $form->textField(
                $contact,
                'last_name',
                array('size' => 40, 'maxlength' => 40, 'onblur' => "findDuplicates($patient->id);",
                'placeholder' => 'Last name',
                'autocomplete' => Yii::app()->params['html_autocomplete'])
            ) ?>
        </td>
      </tr>
      <tr>
        <td>
            <?= $form->label($contact, 'maiden_name') ?>
          <br/>
            <?= $form->error($contact, 'maiden_name') ?>
        </td>
        <td>
            <?= $form->textField(
                $contact,
                'maiden_name',
                array('size' => 40, 'maxlength' => 40, 'placeholder' => 'Maiden name', 'autocomplete' => Yii::app()->params['html_autocomplete'])
            ) ?>
        </td>
      </tr>
      <tr class="patient-duplicate-check">
        <td class=<?= Yii::app()->params['add_patient_fields']['dob'] === 'mandatory' ? 'required':'' ?>>
            <?= $form->label($patient, 'dob') ?>
          <br/>
            <?= $form->error($patient, 'dob') ?>
        </td>
        <td style="text-align: left;">
            <?php
            if ((bool)strtotime($patient->dob)) {
                $patient->dob = str_replace('/', '-', $patient->dob);
                $dob = new DateTime($patient->dob);
                $patient->dob = $dob->format('d/m/Y');
            } else {
                $patient->dob = str_replace('-', '/', $patient->dob);
            }
            ?>
            <?= $form->textField($patient, 'dob', array('onblur' => "findDuplicates($patient->id);",
              'placeholder' => 'dd/mm/yyyy', 'class' => 'date', 'autocomplete'=>'off')) ?>
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
            <?= $form->label($patient, 'patient_source') ?>
          <br/>
            <?= $form->error($patient, 'patient_source') ?>
        </td>
        <td>
          <input type="hidden" name="changePatientSource" id="changePatientSource" value='0'>
            <?= $form->dropDownList(
                $patient,
                'patient_source',
                $patient->getSourcesList(),
                array(
                'options' => array($patient->getScenario() => array('selected' => 'selected')),
                'onchange' => 'document.getElementById("changePatientSource").value ="1"; this.form.submit();',
                )
            ); ?>
        </td>
      </tr>
      <tr>
        <td class="<?= $patient->getScenario() === 'self_register'? 'required':'' ?>">
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
            <?= $form->dropDownList(
                $patient,
                'ethnic_group_id',
                $ethnic_groups,
                array('empty' => '-- select --')
            ); ?>
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
        <td class="<?= (Yii::app()->params['patient_phone_number_mandatory'] === '1') ? "required" : "" ?>">
            <?= $form->label($contact, 'primary_phone') ?>
          <br/>
            <?= $form->error($contact, 'primary_phone') ?>
        </td>
        <td>
            <?= $form->telField($contact, 'primary_phone', array('size' => 15,'placeholder'=>'Phone number', 'maxlength' => 20, 'autocomplete' => Yii::app()->params['html_autocomplete'])) ?>
        </td>
      </tr>
      <tr>
        <td class="<?= $patient->getScenario() === 'self_register'? 'required':'' ?>">
            <?= $form->label($contact, 'email') ?>
          <br/>
            <?= $form->error($contact, 'email') ?>
        </td>
        <td>
            <?= $form->emailField($contact, 'email', array('size' => 15, 'maxlength' => 255, 'placeholder'=>'Email','autocomplete' => Yii::app()->params['html_autocomplete'])) ?>
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
          <td class=<?= Yii::app()->params['add_patient_fields']['hos_num'] === 'mandatory' ? 'required':'' ?>>
                <?= $form->label($patient, 'hos_num') ?>
            <br/>
                <?= $form->error($patient, 'hos_num') ?>
          </td>
          <td>
            <?php if (in_array("admin", Yii::app()->user->getRole(Yii::app()->user->getId()))) {
                echo $form->textField($patient, 'hos_num', array('size' => 40, 'maxlength' => 40, 'placeholder' => $patient->getAttributeLabel('hos_num'), 'autocomplete' => Yii::app()->params['html_autocomplete']));
            } else {
                echo $form->textField($patient, 'hos_num', array('size' => 40, 'maxlength' => 40, 'readonly'=>true, 'placeholder' => $patient->getAttributeLabel('hos_num'), 'autocomplete' => Yii::app()->params['html_autocomplete']));
            }
            ?>
          </td>
        </tr>
        <tr>
          <td>
            <?= \SettingMetadata::model()->getSetting('nhs_num_label')?>
          </td>
          <td>
                <?= $form->textField(
                    $patient,
                    'nhs_num',
                    array(
                      'size' => 40,
                      'maxlength' => 40,
                      'data-child_row' => '.nhs-num-status',
                      'placeholder' => $patient->getAttributeLabel('nhs_num'),
                      'autocomplete' => Yii::app()->params['html_autocomplete']
                    )
                ); ?>
                <?= $form->error($patient, 'nhs_num') ?>
          </td>
        </tr>
<!--        Making the NHS number status to be visible only if use case is not for CERA as they dont want this- CERA-499 -->
        <?php
        if (Yii::app()->params['add_patient_fields']['nhs_num_status']!=='hidden') {?>
             <tr class="nhs-num-status" style="<?= !$patient->nhs_num ? 'display: none;' : '' ?>">
              <td>
                  <?= $form->label($patient, 'nhs_num_status_id') ?>
                <br/>
                  <?= $form->error($patient, 'nhs_num_status_id') ?>
              </td>
              <td>
                  <?= $form->dropDownList(
                      $patient,
                      'nhs_num_status_id',
                      $nhs_num_statuses,
                      array('empty' => '-- select --')
                  ); ?>
              </td>
            </tr>
            <?php
        }
        ?>
        <?= $this->renderPartial('crud/_patient_identifiers', array(
                'form' => $form,
                'patient_identifiers' => $patient_identifiers,
                'patient' => $patient,
            )) ?>
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

                <?= $form->textField($patient, 'date_of_death', array('placeholder' => 'dd/mm/yyy', 'class' => 'date','autocomplete'=>'off')) ?>

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

        <tr id="js-patient-gp-row">
            <td>
                <?php echo \SettingMetadata::model()->getSetting('gp_label'); ?>
                <br/>
                <?php
                /*
                 * Add the errors to the gp label associated with gp first.
                 * If there are no errors on the  GP label from gp_id, then add the errors associated with practice to the gp label itself.
                 */
                echo $form->error($patient, 'gp_id');
                ?>
            </td>
            <td>
                <?php $this->widget('application.widgets.AutoCompleteSearch', ['field_name' => 'autocomplete_gp_id']); ?>
                <div id="selected_gp_wrapper" style="<?= !$patient->gp_id ? 'display: none;' : 'color: white;' ?>">
                    <ul class="oe-multi-select js-selected_gp">
                        <li>
                            <span class="js-name" style="text-align:justify; max-width: 90%;">
                                <?php
                                if (Yii::app()->params['use_contact_practice_associate_model'] === true) {
                                    if ($patient->gp_id && $patient->practice_id) {
                                        $practice_contact_associate = ContactPracticeAssociate::model()->findByAttributes(array('gp_id'=>$patient->gp_id, 'practice_id'=>$patient->practice_id));
                                        $providerNo = isset($practice_contact_associate->provider_no) ? ' ('.$practice_contact_associate->provider_no.') ' : '';
                                        $role = $practice_contact_associate->gp->getGPROle()?' - '.$practice_contact_associate->gp->getGPROle():'';
                                        $practiceNameAddress = $practice_contact_associate->practice ? ($practice_contact_associate->practice->getPracticeNames() ? ' - '.$practice_contact_associate->practice->getPracticeNames():''): '';

                                        echo $practice_contact_associate->gp->getCorrespondenceName().$providerNo.$role.$practiceNameAddress;
                                    }
                                } else {
                                    echo $patient->gp_id ? $patient->gp->CorrespondenceName : '';
                                }
                                ?>
                            </span>
                            <i class="oe-i remove-circle small-icon pad-left js-remove-gp"></i>
                        </li>
                    </ul>
                    <?= CHtml::hiddenField('Patient[gp_id]', $patient->gp_id, array('class' => 'hidden_id')) ?>
                </div>
                <?php if (Yii::app()->user->checkAccess('Create GP') && (SettingMetadata::model()->getSetting('default_country') === 'Australia')) { ?>
                    <a id="js-add-contact-btn1" href="#">Add Referring Practitioner</a>
                <?php } ?>
                <div id="no_gp_result" style="display: none;">
                    <div>No result</div>
                </div>
            </td>
        </tr>
        <?php if (SettingMetadata::model()->getSetting('default_country') !== 'Australia') { ?>
        <tr>
            <td class="<?= $patient->getScenario() === 'referral'? 'required':'' ?>">
                <?= $form->label($patient, 'practice_id') ?>
                <br/>
                <?= $form->error($patient, 'practice_id') ?>
            </td>
            <td>
                <?php $this->widget('application.widgets.AutoCompleteSearch', ['field_name' => 'autocomplete_practice_id']); ?>
                <div id="selected_practice_wrapper" style="<?= !$patient->practice_id ? 'display: none;' : '' ?>">
                    <ul class="oe-multi-select js-selected_practice">
                        <li>
                  <span class="js-name">
                        <?= $patient->practice_id ? $patient->practice->getAddressLines() : '' ?>
                  </span>
                            <i class="oe-i remove-circle small-icon pad-left js-remove-practice"></i>
                        </li>
                    </ul>
                    <?= CHtml::hiddenField(
                        'Patient[practice_id]',
                        $patient->practice_id,
                        array('class' => 'hidden_id')
                    ); ?>
                </div>
                <div id="no_practice_result" style="display: none;">
                    <div>No result</div>
                </div>
            </td>
        </tr>
        <?php } else { ?>
        <?= CHtml::hiddenField(
            'Patient[practice_id]',
            $patient->practice_id,
            array('class' => 'hidden_id')
        ); ?>
        <?php } ?>
        <tr>
            <td class="<?= $patient->getScenario() === 'referral'? 'required':'' ?>">
                <?= $form->label($referral, 'uploadedFile'); ?>
                <br/>
                <?= $form->error($referral, 'uploadedFile')?>
            </td>
            <td>
                <?= $form->fileField($referral, 'uploadedFile'); ?>
            </td>
        </tr>

        <tr>
          <td class="<?= $patient->getScenario() === 'referral'? 'required':'' ?>">
            <?= $form->label($patientuserreferral, 'Referred to') ?>
            <br/>
            <?= $form->error($patientuserreferral, 'user_id') ?>
          </td>
          <td>

                <?php $this->widget('applicaiton.widgets.AutoCompleteSearch', ['field_name'=>'autocomplete_user_id']);?>


            <div id="selected_referred_to_wrapper" style="<?= !$patientuserreferral->user_id ? 'display: none;' : '' ?>">
              <ul class="oe-multi-select js-selected_referral_to">
                <li>
                  <span class="js-name">
                        <?= $patientuserreferral->user_id ? $patientuserreferral->getUserName() : '' ?>
                  </span>
                  <i class="oe-i remove-circle small-icon pad-left js-remove-referral-to"></i>
                </li>
              </ul>

                <?= CHtml::hiddenField(
                    'PatientUserReferral[user_id]',
                    $patientuserreferral->user_id,
                    array('class' => 'hidden_id')
                ); ?>
            </div>
            <div id="no_referred_to_result" style="display: none;">
              <div>No result</div>
            </div>
          </td>
        </tr>

        <?php if (Yii::app()->params['use_contact_practice_associate_model']== true) : ?>
            <tr>
                <td>
                    <label for="contact">Other Practitioner Contacts</label>
                </td>
                <td>
                    <?php $this->widget('application.widgets.AutoCompleteSearch', ['field_name' => 'autocomplete_extra_gps_id']); ?>
                    <div id="selected_extra_gps_wrapper">
                        <ul class="oe-multi-select js-selected_extra_gps">
                            <?php $i=0;
                            if (isset($patient->patientContactAssociates) && !empty($patient->patientContactAssociates)) {
                                foreach ($patient->patientContactAssociates as $patientContactAssociate) {
                                    $gp = $patientContactAssociate->gp;
                                    $practice  = $gp ? $patientContactAssociate->practice: '';
                                    $practiceNameAddress = $practice ? ($practice->getPracticeNames() ? ' - '.$practice->getPracticeNames():''): '';
                                    $role = $gp ? $gp->getGPROle()?' - '.$gp->getGPROle() :'' : '' ;
                                    $practice_contact_associate = ContactPracticeAssociate::model()->findByAttributes(array('gp_id'=>$gp->id, 'practice_id'=>$practice->id));
                                    $providerNo = isset($practice_contact_associate->provider_no) ? ' ('.$practice_contact_associate->provider_no.') ' : '';
                                    //The line below is to ensure a newly added referring practitioner does not show up in the list of contacts also
                                    if ($gp && ($gp->id != $patient->gp_id || $practice->id != $patient->practice_id)) {
                                        ?>
                                        <li>
                                            <span class="js-name" style="text-align:justify; max-width: 90%;">
                                              <?=$gp->getCorrespondenceName().$providerNo.$role.$practiceNameAddress?>
                                            </span>
                                            <i id="js-remove-extra-gp-<?=$gp->id;?>-<?=$practice->id;?>" class="oe-i remove-circle small-icon pad-left js-remove-extra-gps"></i>
                                            <input type="hidden" name="ExtraContact[gp_id][]" class="js-extra-gps" value="<?=$gp->id?>">
                                            <input type="hidden" name="ExtraContact[practice_id][]" class="js-extra-practices" value="<?=$practice->id?>">
                                        </li>
                                    <?php }
                                }
                            }
                            ?>
                        </ul>
                    </div>
                    <?php if (Yii::app()->user->checkAccess('Create GP')) { ?>
                        <a id="js-add-contact-btn2" href="#">Add New Practitioner Contact</a>
                    <?php } ?>
                    <div id="no_extra_gps_result" style="display: none;">
                        <div>No result</div>
                    </div>
                </td>
            </tr>
        <?php endif; ?>

        <?php
        if (Yii::app()->controller->action->id == 'update') {?>
            <tr>
                <td>
                    <?= $form->label($patient, 'created_date') ?>
                </td>
                <td>
                    <label for="patient_create_date"><?= date("d-M-Y h:i a", strtotime($patient->created_date))?></label>
                </td>
            </tr>
            <tr>
                <td>
                    <?= $form->label($patient, 'last_modified_date') ?>
                </td>
                <td>
                    <label for="patient_create_date"><?= date("d-M-Y h:i a", strtotime($patient->last_modified_date))?></label>
                </td>
            </tr>
        <?php } ?>
        </tbody>
      </table>
    </div>
    <div class="row flex-layout">
        <?= CHtml::link('Cancel', ( $patient->isNewRecord ? Yii::app()-> createURL('site/index') : ( isset($prevUrl) ? Yii::app()->createUrl($prevUrl) : null ) ), array('class' => 'button blue hint')); ?>
        <?= CHtml::submitButton(
            $patient->isNewRecord ? 'Create new patient' : 'Save patient',
            array('class' => 'button green hint')
        ); ?>
    </div>
  </div>
</div>
<?php $this->endWidget(); ?>

<?php
if (SettingMetadata::model()->getSetting('default_country') === 'Australia') {
    $practicecontact = new Contact('manage_practice');
    $practiceaddress = new Address('manage_practice');
    $practice = new Practice('manage_practice');
    $this->renderPartial('../practice/create_practice_form',
                         array('model'=>$practice, 'address'=>$practiceaddress, 'contact'=>$practicecontact, 'context'=>'AJAX')
    );

    $gpcontact = new Contact('manage_gp');
    $patientReferralContact = new Contact('manage_gp');
    $this->renderPartial('../gp/create_gp_form',
                         array(
                             'model' => $gpcontact,
                             'context' => 'AJAX',
                             'id' => 'js-add-gp-event'
                         ),
                         false);

    $extra_gp_contact = new Contact('manage_gp');
    $extra_practice = new Practice('manage_practice');
    $extra_practice_address = new Address('manage_practice');
    $extra_practice_contact = new Contact('manage_practice');
    $extra_practice_associate = new ContactPracticeAssociate();
    $this->renderPartial('../patient/crud/create_contact_form',
                         array(
                             'extra_gp_contact' => $extra_gp_contact,
                             'extra_practice'=>$extra_practice,
                             'extra_practice_address'=>$extra_practice_address,
                             'extra_practice_contact'=>$extra_practice_contact,
                             'extra_practice_associate' => $extra_practice_associate,
                             'context' => 'AJAX',
                         ),
                         false);
?>
<script>
    console.log('Got to this point');

    $('.js-cancel-add-practitioner').click(function(event){
        event.preventDefault();
        $("#gp-form")[0].reset();
        $("#errors").text("");
        $(".alert-box").css("display","none");
        $('.js-add-practitioner-event').css('display','none');

    });
    $('#js-add-gp-btn').click(function(event){
        $('#js-add-gp-event').css('display','');
        $('#gp_adding_title').data('type','gp');
        $('#gp_adding_title').html('Add General Practitioner')
        return false;
    });

    $('#js-add-contact-btn1').click(function(event){
        $('#extra_gp_adding_title').text("Add Referring Practitioner");
        $('#extra_gp_adding_form').css('display','');
        return false;
    });

    $('#js-add-contact-btn2').click(function(event){
        $('#extra_gp_adding_title').text("Add New Practitioner Contact");
        $('#extra_gp_adding_form').css('display','');
        return false;
    });


    $('.js-cancel-add-contact').click(function(event){
        event.preventDefault();
        extraContactFormCleaning();
        $(".js-extra-practice-gp-id").val("");
        // clearing the selected gp role id if user has closed the popup.
        $(".js-extra-gp-contact-label-id").val("");
        // enabling title, phone number and provider no on closing the popup.
        $("#extra-gp-form #Contact_title").prop("readonly", false);
        $("#extra-gp-form #Contact_primary_phone").prop("readonly", false);
        // remove data from hidden fields.
        $('.gp_data_retrieved').val("");

        $('#extra-gp-message').hide();
        // unsetting the variable (defined in create_contact_form inside the onselect function of autocompletesearch widget - firstname and lastname field)
        gp = new Gp();
    });

    $('#js-add-extra-practice-btn').click(function(event){
        event.preventDefault();
        extraContactFormCleaning();
        $('#extra_practice_adding_new_form').css('display','');
    });

    $('.js-remove-extra-gps').click(function(event){
        event.preventDefault();
        $(this).parent('li').remove();
    });

    $('#js-cancel-add-practice').click(function (event) {
        event.preventDefault();
        $("#practice-form")[0].reset();
        $("#errors").text("");
        $("#practice-alert-box").css("display","none");
        $('#js-add-practice-event').css('display', 'none');
    });
    $('#js-add-practice-btn').click(function (event) {
        $('#js-add-practice-event').css('display', '');
        return false;
    });

    function addGpItem(type, ui){
        var $wrapper = $('#selected_'+type+'_wrapper');
        var JsonObj = JSON.parse(ui);
        $wrapper.find('.js-name').text(JsonObj.label);
        $wrapper.show();
        $wrapper.find('.hidden_id').val(JsonObj.id);
    }

    function extraContactFormCleaning(){
        $("#extra-gp-form")[0].reset();
        $("#extra_gp_errors").text("");
        $("#extra_gp_practitioner-alert-box").css("display","none");
        $('#extra_gp_adding_form').css('display','none');
        $('#extra_gp_selected_contact_label_wrapper').css('display','none');
        $('#extra_gp_selected_contact_label_wrapper').find('.js-name').html("");

        $("#extra-adding-existing-practice-form")[0].reset();
        $("#extra-existing-practice-errors").text("");
        $("#extra-existing-practice-alert-box").css("display","none");
        $('#extra_practice_adding_existing_form').css('display','none');
        $('.js-selected-practice-associate').find('li').remove();


        $("#extra-adding-practice-form")[0].reset();
        $("#extra-practice-errors").text("");
        $("#extra-practice-practice-alert-box").css("display","none");
        $('#extra_practice_adding_new_form').css('display','none');
        $("#extra_practice_adding_existing_form");
    }
</script>
<?php } ?>

<script>
    OpenEyes.UI.AutoCompleteSearch.init({
        input: $('#autocomplete_extra_gps_id'),
        url: '/patient/gpListRp',
        maxHeight: '200px',
        onSelect: function(){
            let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
            let addGp = true;
            // traversing the li's to make sure we don't have duplicates (i.e. combination of gpid and practiceid)
            $.each($('.js-selected_extra_gps li'), function() {
                var gpId = $(this).find('.js-extra-gps').val();
                var practiceId = $(this).find('.js-extra-practices').val();

                if (gpId === AutoCompleteResponse.value && practiceId === AutoCompleteResponse.practiceId){
                    addGp = false;
                    return addGp;
                }
            });

            // If the combination of gpid and practiceid does not already exist in the list then add it to the list.
            if(addGp){
                addExtraGp('js-selected_extra_gps', AutoCompleteResponse.value, AutoCompleteResponse.practiceId);
            }
        }
    });

    OpenEyes.UI.AutoCompleteSearch.init({
    input: $('#autocomplete_gp_id'),
    url: '/patient/gpListRp',
      maxHeight: '200px',
      onSelect: function(){
      let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
      removeSelectedGP();
      addItemPatientForm('selected_gp_wrapper', {item: AutoCompleteResponse}, <?= Yii::app()->params['use_contact_practice_associate_model'] === true  ? 'true' : 'false'; ?>);
    }
    });

    OpenEyes.UI.AutoCompleteSearch.init({
    input: $('#autocomplete_practice_id'),
    url: '/patient/practiceList',
    maxHeight: '200px',
    onSelect: function(){
      let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
        removeSelectedPractice();
        addItemPatientForm('selected_practice_wrapper', {item: AutoCompleteResponse});
    }
    });
    OpenEyes.UI.AutoCompleteSearch.init({
      input: $('#autocomplete_user_id'),
      url: '/user/autocomplete',
      maxHeight: '200px',
      onSelect: function(){
          let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
          removeSelectedReferredto();
          addItemPatientForm('selected_referred_to_wrapper', {item: AutoCompleteResponse});
      }
    });

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
            });
        }
    }

    function addExtraGp(id, gpId, practiceId){
        $.ajax({
            url: "<?php echo Yii::app()->controller->createUrl('practiceAssociate/getGpWithPractice'); ?>",
            data: {id : id, gp_id : gpId, practice_id : practiceId},
            type: 'GET',
            success: function (response) {
                response = JSON.parse(response);
                if(id == 'js-selected_gp'){
                    $('.'+id).html(response.content);
                }else if(id == 'js-selected_extra_gps'){
                    $('.'+id).append(response.content);
                    var wrapper = $('#selected_extra_gps_wrapper');
                    wrapper.find('.js-name').attr('style','text-align:justify; max-width: 90%;');
                }
                $('#js-remove-extra-gp-' + response.gp_id + '-' + response.practice_id).click(function(){
                    // If else condition is added to handle both the cases (i.e. when removing contact/gp) as they have been implemented differently.
                    if(id == 'js-selected_gp'){
                        // For Gp
                        $(this).parent('li').find('span').text('');
                        $(this).parent('li').find('input').remove();
                        $(this).parent('li').hide();
                    } else {
                        // For contacts
                        $(this).parent('li').remove();
                    }
                });
                if(id == 'js-selected_gp'){
                    var wrapper = $('#selected_gp_wrapper');
                    wrapper.find('.js-name').text(response.label);
                    wrapper.find('.hidden_id').val(response.gp_id);
                    $('#Patient_practice_id').val(response.practice_id);
                    $('#prac_id').val(response.practice_id);
                    wrapper.show();
                }
            }
        }
        )
    }

    //CERA-564 Ensuring pressing Enter key on First name, last name or dob does not submit the form and instead gives a chance for any duplicate patient warning messages to appear
    $("#Contact_first_name, #Contact_last_name").keypress(function(event){
        if (event.which == '13') {
            event.preventDefault();
            $(this).blur();
        }
    });

    $("#Patient_dob").keypress(function(event){
        if (event.which == '13') {
            event.preventDefault();
            $("#Patient_dob").blur();
            $(".pickmeup").addClass("pmu-hidden");
        }
    });

</script>
