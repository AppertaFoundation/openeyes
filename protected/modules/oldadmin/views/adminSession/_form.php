<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

?>
<div class="form">

	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'session-form',
		'enableAjaxValidation' => false,
	)); ?>

	<p class="note">
		Fields with <span class="required">*</span> are required.
	</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'sequence_id'); ?>
		<div class="field">
			<?php echo $form->textField($model, 'sequence_id', array('disabled' => true)); ?>
			<?php echo $form->error($model, 'sequence_id'); ?>
		</div>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model, 'date'); ?>
		<div class="field">
			<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
				'model' => $model,
				'attribute' => 'date',
			  'value' => $model->date,
			  'options' => array(
			    'showAnim' => 'fold',
					'minDate' => 'new Date()',
					'defaultDate' => $model->date,
					'dateFormat' => Helper::NHS_DATE_FORMAT_JS
			  ),
			  'htmlOptions'=>array(
			  	'style' => 'height: 20px;'
			  ),
				)); ?>
			<?php echo $form->error($model, 'date'); ?>
		</div>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'start_time'); ?>
		<div class="field">
			<?php echo $form->textField($model, 'start_time'); ?>
			<?php echo $form->error($model, 'start_time'); ?>
		</div>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'end_time'); ?>
		<div class="field">
			<?php echo $form->textField($model, 'end_time'); ?>
			<?php echo $form->error($model, 'end_time'); ?>
		</div>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'firm'); ?>
		<div class="field">
			<?php echo $form->dropDownList($model->firmAssignment, 'firm_id', Firm::model()->getListWithSpecialties(), array('empty' => 'None')); ?>
			<?php echo $form->error($model, 'firm'); ?>
		</div>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'theatre'); ?>
		<div class="field">
			<?php echo $form->dropDownList($model,'theatre_id', Theatre::model()->getListWithSites(), array('empty' => '')); ?>
			<?php echo $form->error($model, 'theatre_id'); ?>
		</div>
	</div>

	<div class="row nolabel">
		<div class="field">
			<?php echo $form->checkBox($model, 'consultant'); ?>
			<?php echo $form->labelEx($model,'consultant'); ?>
			<?php echo $form->error($model,'consultant'); ?>
		</div>
	</div>
	
	<div class="row nolabel">
		<div class="field">
			<?php echo $form->checkBox($model, 'paediatric'); ?>
			<?php echo $form->labelEx($model,'paediatric'); ?>
			<?php echo $form->error($model,'paediatric'); ?>
		</div>
	</div>
	
	<div class="row nolabel">
		<div class="field">
			<?php echo $form->checkBox($model, 'anaesthetist'); ?>
			<?php echo $form->labelEx($model,'anaesthetist'); ?>
			<?php echo $form->error($model,'anaesthetist'); ?>
		</div>
	</div>

	<div class="row nolabel">
		<div class="field">
			<?php echo $form->checkBox($model, 'general_anaesthetic'); ?>
			<?php echo $form->labelEx($model,'general_anaesthetic'); ?>
			<?php echo $form->error($model,'general_anaesthetic'); ?>
		</div>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'status'); ?>
		<div class="field">
			<?php echo $form->radioButtonList($model, 'status', $model->getStatusOptions()); ?>
			<?php echo $form->error($model,'status'); ?>
		</div>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'comments'); ?>
		<div class="field">
			<?php echo $form->textArea($model,'comments', array('rows'=>10, 'cols'=>40)); ?>
			<?php echo $form->error($model,'comments'); ?>
		</div>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

	<?php $this->endWidget(); ?>

</div>
<!-- form -->
