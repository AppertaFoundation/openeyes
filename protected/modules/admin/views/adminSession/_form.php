<?php
/*
_____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk   info@openeyes.org.uk
--
*/

?>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'session-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row"><?php echo $model->sequence_id; ?></div>
	<div class="row"><?php echo $model->date; ?></div>
	<div class="row"><?php echo $model->start_time; ?></div>
	<div class="row"><?php echo $model->end_time; ?></div>

	<div class="row">
		<input id="Session_consultant" value="1" type="checkbox" name="Session[consultant]" 
		<?php if($model->consultant) { ?>checked="checked"<?php } ?> />
		<?php echo $form->labelEx($model,'consultant'); ?>
		<?php echo $form->error($model,'consultant'); ?>
	</div>
	
	<div class="row">
		<input id="Session_paediatric" value="1" type="checkbox" name="Session[paediatric]" 
		<?php if($model->paediatric) { ?>checked="checked"<?php } ?> />
		<?php echo $form->labelEx($model,'paediatric'); ?>
		<?php echo $form->error($model,'paediatric'); ?>
	</div>
	
	<div class="row">
		<input id="Session_anaesthetist" value="1" type="checkbox" name="Session[anaesthetist]" 
		<?php if($model->anaesthetist) { ?>checked="checked"<?php } ?> />
		<?php echo $form->labelEx($model,'anaesthetist'); ?>
		<?php echo $form->error($model,'anaesthetist'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'status');
		$i = 0;
		foreach ($model->getStatusOptions() as $value => $name) { ?>
		<input id="Session_status_<?php echo $i; ?>" value="<?php echo $value; ?>" type="radio" name="Session[status]"<?php
			if ($model->status == $value) {
				echo ' checked="checked"';
			} ?> /> <label for="Session_status_<?php echo $i; ?>" style="display: inline; font-weight: normal;"><?php echo $name; ?></label> &nbsp; <?php
		}
		echo $form->error($model,'status'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'comments'); ?>
		<?php echo $form->textArea($model,'comments', array('rows'=>10, 'cols'=>40)); ?>
		<?php echo $form->error($model,'comments'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
