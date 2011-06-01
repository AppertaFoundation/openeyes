Allergies: <br />

	<div class="row">
		<label for="ElementAllergies_value">Phrase:</label>
		<?php echo CHtml::dropDownList('ElementAllergies[phrase]', '', $model->getPhraseBySpecialtyOptions('Allergies'),
			array('onChange' => 'appendText($(this), $("#ElementAllergies_value"));')); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'value'); ?>
		<?php echo $form->textArea($model,'value',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'value'); ?>
	</div>
