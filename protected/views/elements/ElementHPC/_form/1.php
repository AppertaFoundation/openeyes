HPC: <br />

	<div class="row">
		<label for="ElementHPC_value">History:</label>
		<?php echo CHtml::dropDownList('ElementHPC[phrase]', '', $model->getPhraseBySpecialtyOptions('History'),
			array('onChange' => 'appendText($(this), $("#ElementHPC_value"));')); ?>
	</div>

	<div class="row">
		<label for="ElementHPC_value">Severity:</label>
		<?php echo CHtml::dropDownList('ElementHPC[phrase]', '', $model->getPhraseBySpecialtyOptions('Severity'),
			array('onChange' => 'appendText($(this), $("#ElementHPC_value"));')); ?>
	</div>

	<div class="row">
		<label for="ElementHPC_value">Onset:</label>
		<?php echo CHtml::dropDownList('ElementHPC[phrase]', '', $model->getPhraseBySpecialtyOptions('Onset'),
			array('onChange' => 'appendText($(this), $("#ElementHPC_value"));')); ?>
	</div>

	<div class="row">
		<label for="ElementHPC_value">Site:</label>
		<?php echo CHtml::dropDownList('ElementHPC[phrase]', '', $model->getPhraseBySpecialtyOptions('Site'),
			array('onChange' => 'appendText($(this), $("#ElementHPC_value"));')); ?>
	</div>

	<div class="row">
		<label for="ElementHPC_value">Duration:</label>
		<?php echo CHtml::dropDownList('ElementHPC[phrase]', '', $model->getPhraseBySpecialtyOptions('Duration'),
			array('onChange' => 'appendText($(this), $("#ElementHPC_value"));')); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'value'); ?>
		<?php echo $form->textArea($model,'value',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'value'); ?>
	</div>
