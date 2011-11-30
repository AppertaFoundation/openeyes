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

?><div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'sequence-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary(array($model, $firm)); ?>

	<div class="row">
		<?php echo $form->labelEx($firm,'firm_id'); ?>
		<?php echo $form->dropDownList($firm,'firm_id',$firm->getFirmOptions(),
			array('empty' => '')); ?>
		<?php echo $form->error($firm,'firm_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'theatre_id'); ?>
		<?php echo $form->dropDownList($model,'theatre_id',$model->getTheatreOptions(),
			array('empty' => '')); ?>
		<?php echo $form->error($model,'theatre_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'start_date'); ?>
		<?php
$this->widget('zii.widgets.jui.CJuiDatePicker', array(
	'model'=>$model,
	'attribute'=>'start_date',
    'value'=>$model->start_date,
    // additional javascript options for the date picker plugin
    'options'=>array(
        'showAnim'=>'fold',
		'minDate'=>'new Date()',
		'defaultDate'=>$model->start_date,
		'dateFormat'=>'d-M-yy'
    ),
    'htmlOptions'=>array(
        'style'=>'height:20px;'
    ),
)); ?>
		<?php echo $form->error($model,'start_date'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'start_time'); ?>
		<?php echo $form->textField($model,'start_time'); ?>
		<?php echo $form->error($model,'start_time'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'end_time'); ?>
		<?php echo $form->textField($model,'end_time'); ?>
		<?php echo $form->error($model,'end_time'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'end_date'); ?>
		<?php
$this->widget('zii.widgets.jui.CJuiDatePicker', array(
	'model'=>$model,
	'attribute'=>'end_date',
    'value'=>$model->end_date,
    // additional javascript options for the date picker plugin
    'options'=>array(
        'showAnim'=>'fold',
		'minDate'=>'new Date()',
		'defaultDate'=>$model->end_date,
		'dateFormat'=>'d-M-yy'
    ),
    'htmlOptions'=>array(
        'style'=>'height:20px;'
    ),
)); ?>
		<?php echo $form->error($model,'end_date'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'week_selection');
		$i = 0;
		foreach ($model->getWeekSelectionOptions() as $value => $name) { ?>
		<input id="Sequence_week_selection_<?php echo $i; ?>" value="<?php echo $value; ?>" type="checkbox" name="Sequence[week_selection][]"<?php
			if (($model->week_selection & $value) == $value) {
				echo ' checked="checked"';
			} ?> /> <label for="Sequence_week_selection_<?php echo $i; ?>" style="display: inline; font-weight: normal;"><?php echo $name; ?></label> &nbsp; <?php
		}
		echo $form->error($model,'week_selection'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'repeat_interval'); ?>
		<?php echo $form->dropDownList($model,'repeat_interval',$model->getFrequencyOptions()) . ' (Leave as-is if using week selection above)'; ?>
		<?php echo $form->error($model,'repeat_interval'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
