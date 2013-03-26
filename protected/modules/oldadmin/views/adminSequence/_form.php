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

	<?php $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
		'id' => 'sequence-form',
		'enableAjaxValidation' => false,
	)); ?>

	<p class="note">
		Fields with <span class="required">*</span> are required.
	</p>

	<?php echo $form->errorSummary(array($model)); ?>

	<div class="row">
		<?php echo $form->labelEx($model, 'firm'); ?>
		<div class="field">
			<?php echo $form->dropDownList($model->firmAssignment, 'firm_id', Firm::model()->getListWithSpecialties(), array('empty' => 'None')); ?>
			<?php echo $form->error($model, 'firm'); ?>
		</div>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'theatre_id'); ?>
		<div class="field">
			<?php echo $form->dropDownList($model,'theatre_id', Theatre::model()->getListWithSites(), array('empty' => '')); ?>
			<?php echo $form->error($model, 'theatre_id'); ?>
		</div>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'start_date'); ?>
		<div class="field">
			<?php echo $form->datePicker($model, 'start_date', array('mindate' => 'new Date()'), array('style' => 'height: 20px;'))?>
			<?php echo $form->error($model,'start_date'); ?>
		</div>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'start_time'); ?>
		<div class="field">
			<?php echo $form->textField($model,'start_time'); ?>
			<?php echo $form->error($model,'start_time'); ?>
		</div>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'end_time'); ?>
		<div class="field">
			<?php echo $form->textField($model,'end_time'); ?>
			<?php echo $form->error($model,'end_time'); ?>
		</div>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'end_date'); ?>
		<div class="field">
			<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
				'model' => $model,
				'attribute' => 'end_date',
	    		'value' => $model->end_date,
	    		'options' => array(
	      			'showAnim' => 'fold',
					'minDate' => 'new Date()',
					'defaultDate' => $model->end_date,
					'dateFormat' => Helper::NHS_DATE_FORMAT_JS
	    		),
	    		'htmlOptions' => array(
	      		'style' => 'height: 20px;'
	    		),
				)); ?>
			<?php echo $form->error($model,'end_date'); ?>
		</div>
	</div>
	
	<div class="row nolabel">
		<div class="field">
			<input id="Sequence_consultant" value="1" type="checkbox" name="Sequence[consultant]" 
			<?php if($model->consultant) { ?>checked="checked"<?php } ?> />
			<?php echo $form->labelEx($model,'consultant'); ?>
			<?php echo $form->error($model,'consultant'); ?>
		</div>
	</div>
	
	<div class="row nolabel">
		<div class="field">
			<input id="Sequence_paediatric" value="1" type="checkbox" name="Sequence[paediatric]" 
			<?php if($model->paediatric) { ?>checked="checked"<?php } ?> />
			<?php echo $form->labelEx($model,'paediatric'); ?>
			<?php echo $form->error($model,'paediatric'); ?>
		</div>
	</div>
	
	<div class="row nolabel">
		<div class="field">
			<input id="Sequence_anaesthetist" value="1" type="checkbox" name="Sequence[anaesthetist]" 
			<?php if($model->anaesthetist) { ?>checked="checked"<?php } ?> />
			<?php echo $form->labelEx($model,'anaesthetist'); ?>
			<?php echo $form->error($model,'anaesthetist'); ?>
		</div>
	</div>

	<div class="row nolabel">
		<div class="field">
			<input id="Sequence_general_anaesthetic" value="1" type="checkbox" name="Sequence[general_anaesthetic]" 
			<?php if($model->general_anaesthetic) { ?>checked="checked"<?php } ?> />
			<?php echo $form->labelEx($model,'general_anaesthetic'); ?>
			<?php echo $form->error($model,'general_anaesthetic'); ?>
		</div>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'repeat_interval'); ?>
		<div class="field">
			<?php echo $form->dropDownList($model,'repeat_interval',$model->getFrequencyOptions()); ?>
			<?php echo $form->error($model,'repeat_interval'); ?>
		</div>
	</div>

	<div id="week_selection" class="row">
		<?php echo $form->labelEx($model,'week_selection'); ?>
		<div class="field">
			<?php 
			$i = 0;
			foreach ($model->getWeekSelectionOptions() as $value => $name) { ?>
			<input id="Sequence_week_selection_<?php echo $i; ?>" value="<?php echo $value; ?>" type="checkbox" name="Sequence[week_selection][]"<?php
				if (($model->week_selection & $value) == $value) {
					echo ' checked="checked"';
				} ?> /> <label for="Sequence_week_selection_<?php echo $i; ?>" style="display: inline; font-weight: normal;"><?php echo $name; ?></label> &nbsp; <?php
			}
			echo $form->error($model,'week_selection'); ?>
		</div>
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
				$('#week_selection').show();
			} else {
				$('#week_selection').hide();
			}
		}
	</script>
	
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

	<?php $this->endWidget(); ?>

</div><!-- form -->
