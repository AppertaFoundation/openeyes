FOH: <br />

	<div class="row">
		<label for="ElementFOH_value">Phrase:</label>
		<?php echo CHtml::dropDownList('ElementFOH[phrase]', '', $model->getExamPhraseOptions(ExamPhrase::PART_FOH),
			array('onChange' => 'appendText($(this), $("#ElementFOH_value"));')); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'value'); ?>
		<?php echo $form->textArea($model,'value',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'value'); ?>
	</div>