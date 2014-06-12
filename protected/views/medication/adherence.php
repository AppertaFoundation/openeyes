<?php $form = $this->beginWidget('FormLayout', array('layoutColumns' => array('label' => 3, 'field' => 9),)); ?>
	<input type="hidden" name="patient_id" id="medication_id" value="<?= $patient->id ?>"/>
	<fieldset class="field-row">
		<legend><strong>Adherence</strong></legend>
		<div class="row field-row">
			<div class="<?= $form->columns('label') ?>"><label for="adherence">Adherence:</label></div>
			<div class="<?= $form->columns('field') ?>">
				<?php echo CHtml::dropDownList('medication_adherence_level', '', CHtml::listData(MedicationAdherenceLevel::model()->findAll(array('order' => 'display_order')), 'id', 'name'))?>
			</div>
		</div>
	</fieldset>
	<div class="buttons">
		<button type="button" class="medication_save secondary small">Save</button>
		<button type="button" class="medication_cancel warning small">Cancel</button>
	</div>
<? $this->endWidget(); ?>