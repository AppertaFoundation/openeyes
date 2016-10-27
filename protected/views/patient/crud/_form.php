<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<?php
/* @var $this PatientController */
/* @var $patient Patient */
/* @var $form CActiveForm */

$nhs_num_statuses = CHtml::listData( NhsNumberVerificationStatus::model()->findAll(), 'id', 'description');
$countries = CHtml::listData( Country::model()->findAll(), 'id', 'name');
$address_type_ids = CHtml::listData(AddressType::model()->findAll(), 'id', 'name');

$general_practitioners = CHtml::listData(Gp::model()->findAll(), 'id', 'correspondenceName');

$practice_models = Practice::model()->findAll();
foreach($practice_models as $practice_model){
    if ($practice_model->contact->address){
        $practices[$practice_model->id] = $practice_model->contact->address->letterLine;
    }
}

$gender_models = Gender::model()->findAll();
$genders = CHtml::listData($gender_models, function($gender_model){
    return CHtml::encode($gender_model->name)[0];
}, 'name');

$ethnic_groups = CHtml::listData(EthnicGroup::model()->findAll(), 'id', 'name');
        
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id' => 'patient-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation' => true,
)); ?>

	<p class="note text-right">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($patient); ?>

        <div class="row field-row">
            <div class="large-3 column"><?php echo $form->labelEx($patient,'hos_num'); ?></div>
            <div class="large-4 column end">
                <?php echo $form->textField($patient,'hos_num',array('size'=>40,'maxlength'=>40)); ?>
                <?php echo $form->error($patient,'hos_num'); ?>
            </div>
	</div>
	<div class="row field-row">
            <div class="large-3 column nhs-number-wrapper">
                <div class="nhs-number warning">
                    <span class="hide-text print-only">NHS Number:</span>
                </div>
                <div>Number</div>
            </div>
            <div class="large-4 column end">
                <?php echo $form->textField($patient,'nhs_num',array('size' => 40,'maxlength' => 40, 'data-child_row' => '.nhs-num-status')); ?>
                <?php echo $form->error($patient,'nhs_num'); ?>
            </div>
	</div>
        <div class="row field-row nhs-num-status <?php echo (!$patient->nhs_num ? 'hide':''); ?>">
            <div class="large-3 column"><?php echo $form->labelEx($patient,'nhs_num_status_id'); ?></div>
            <div class="large-4 column end">
                <?php echo $form->dropDownList($patient,'nhs_num_status_id', $nhs_num_statuses, array('empty'=>'-- select --')); ?>
                <?php echo $form->error($patient,'nhs_num_status_id'); ?>
            </div>
	</div>
        <hr>
        <!-- -->
        <div class="row field-row">
            <div class="large-3 column"><?php echo $form->labelEx($contact,'title'); ?></div>
            <div class="large-4 column end">
                <?php echo $form->textField($contact,'title',array('size'=>40,'maxlength'=>40)); ?>
                <?php echo $form->error($contact,'title'); ?>
            </div>
	</div>
        <div class="row field-row">
            <div class="large-3 column"><?php echo $form->labelEx($contact,'first_name'); ?></div>
            <div class="large-4 column end">
                <?php echo $form->textField($contact,'first_name',array('size'=>40,'maxlength'=>40)); ?>
                <?php echo $form->error($contact,'first_name'); ?>
            </div>
	</div>
        
        <div class="row field-row">
            <div class="large-3 column"><?php echo $form->labelEx($contact,'last_name'); ?></div>
            <div class="large-4 column end">
                <?php echo $form->textField($contact,'last_name',array('size'=>40,'maxlength'=>40)); ?>
                <?php echo $form->error($contact,'last_name'); ?>
            </div>
	</div>
       
        <!-- -->
        
        <div class="row field-row">
            <div class="large-3 column"><?php echo $form->labelEx($patient,'dob'); ?></div>
            <div class="large-4 column end">
                <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        'name' => 'Patient[dob]',
                        'id' => 'date_from',
                        'options' => array(
                            'showAnim' => 'fold',
                            'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                        ),
                        'value' => Helper::convertMySQL2NHS($patient->dob, ''),
                        'htmlOptions' => array(
                            'class' => 'small fixed-width',
                        ),
                    ))?>
                <?php echo $form->error($patient,'dob'); ?>
            </div>
	</div>
        <div class="row field-row">
            <div class="large-3 column"><?php echo $form->labelEx($patient,'gender'); ?></div>
            <div class="large-4 column end">
                <?php echo $form->dropDownList($patient,'gender', $genders, array('empty'=>'-- select --')); ?>
                <?php echo $form->error($patient,'gender'); ?>
            </div>
	</div>
        
        <div class="row field-row">
            <div class="large-3 column"><?php echo $form->labelEx($patient,'ethnic_group_id'); ?></div>
            <div class="large-4 column end">
                <?php echo $form->dropDownList($patient,'ethnic_group_id', $ethnic_groups, array('empty'=>'-- select --')); ?>
                <?php echo $form->error($patient,'ethnic_group_id'); ?>
            </div>
	</div>
        
        <hr>
        
        <?php $this->renderPartial('_form_address', array('form' => $form, 'address' => $address, 'countries' => $countries, 'address_type_ids' => $address_type_ids)); ?>
        
        <hr>
        <div class="row field-row">
            <div class="large-3 column"><?php echo $form->labelEx($contact,'primary_phone'); ?></div>
            <div class="large-4 column end">
                <?php echo $form->telField($contact,'primary_phone',array('size'=>15,'maxlength'=>20)); ?>
                <?php echo $form->error($contact,'primary_phone'); ?>
            </div>
	</div>
        
        <div class="row field-row">
            <div class="large-3 column"><?php echo $form->labelEx($address,'email'); ?></div>
            <div class="large-4 column end">
                <?php echo $form->emailField($address,'email',array('size'=>15, 'maxlength'=>255)); ?>
                <?php echo $form->error($address,'email'); ?>
            </div>
	</div>
        
        <hr>
        
        <div class="row field-row">
            <div class="large-3 column"><?php echo $form->labelEx($patient,'is_deceased'); ?></div>
            <div class="large-4 column end">
                <?php echo $form->checkBox($patient,'is_deceased', array('data-child_row' => '.date_of_death')); ?>
                <?php echo $form->error($patient,'is_deceased'); ?>
            </div>
	</div>
        <div class="row field-row date_of_death <?php echo ($patient->is_deceased == 0 ? 'hide':''); ?>">
            <div class="large-3 column"><?php echo $form->labelEx($patient,'date_of_death'); ?></div>
            <div class="large-4 column end">
            <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        'name' => 'Patient[date_of_death]',
                        'id' => 'date_to',
                        'options' => array(
                            'showAnim' => 'fold',
                            'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                        ),
                        'value' => Helper::convertMySQL2NHS($patient->date_of_death, ''),
                        'htmlOptions' => array(
                            'class' => 'small fixed-width',
                        ),
                    ))?>
                <?php echo $form->error($patient,'date_of_death'); ?>
            </div>
	</div>

        <hr>
        
<!--        
	<div class="row field-row">
            <div class="large-3 column"><?php echo $form->labelEx($patient,'pas_key'); ?></div>
            <div class="large-4 column end">
                <?php echo $form->textField($patient,'pas_key',array('size'=>10,'maxlength'=>10)); ?>
                <?php echo $form->error($patient,'pas_key'); ?>
            </div>
	</div>
-->
        <div class="row field-row">
            <div class="large-3 column"><?php echo $form->labelEx($patient,'gp_id'); ?></div>
            <div class="large-4 column end">
                <?php echo $form->dropDownList($patient,'gp_id', $general_practitioners, array('empty'=>'-- select --')); ?>
                <?php echo $form->error($patient,'gp_id'); ?>
            </div>
	</div>

        <div class="row field-row">
            <div class="large-3 column"><?php echo $form->labelEx($patient,'practice_id'); ?></div>
            <div class="large-4 column end">
                <?php echo $form->dropDownList($patient,'practice_id', $practices, array('empty'=>'-- select --')); ?>
                <?php echo $form->error($patient,'practice_id'); ?>
            </div>
	</div>

	<div class="row buttons text-right">
            <?php echo CHtml::submitButton($patient->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->