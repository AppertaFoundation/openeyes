<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
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
					'dateFormat'=>Helper::NHS_DATE_FORMAT_JS
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
		'dateFormat'=>Helper::NHS_DATE_FORMAT_JS
    ),
    'htmlOptions'=>array(
        'style'=>'height:20px;'
    ),
)); ?>
		<?php echo $form->error($model,'end_date'); ?>
	</div>
	
	<div class="row">
		<input id="Sequence_consultant" value="1" type="checkbox" name="Sequence[consultant]" 
		<?php if($model->consultant) { ?>checked="checked"<?php } ?> />
		<?php echo $form->labelEx($model,'consultant'); ?>
		<?php echo $form->error($model,'consultant'); ?>
	</div>
	
	<div class="row">
		<input id="Sequence_paediatric" value="1" type="checkbox" name="Sequence[paediatric]" 
		<?php if($model->paediatric) { ?>checked="checked"<?php } ?> />
		<?php echo $form->labelEx($model,'paediatric'); ?>
		<?php echo $form->error($model,'paediatric'); ?>
	</div>
	
	<div class="row">
		<input id="Sequence_anaesthetist" value="1" type="checkbox" name="Sequence[anaesthetist]" 
		<?php if($model->anaesthetist) { ?>checked="checked"<?php } ?> />
		<?php echo $form->labelEx($model,'anaesthetist'); ?>
		<?php echo $form->error($model,'anaesthetist'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'repeat_interval'); ?>
		<?php echo $form->dropDownList($model,'repeat_interval',$model->getFrequencyOptions()); ?>
		<?php echo $form->error($model,'repeat_interval'); ?>
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

	<script type="text/javascript">
		$(function() {
			toggleWeekSelection();
			$('#Sequence_repeat_interval').change(function() {
				toggleWeekSelection()
			});
		});
		function toggleWeekSelection() {
			if($('#Sequence_repeat_interval').val() == 5) {
				$('#Sequence_week_selection_0').parent().show();
			} else {
				$('#Sequence_week_selection_0').parent().hide();
			}
		}
	</script>
	
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
