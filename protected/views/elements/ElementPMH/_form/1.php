PMH: <br />

	<div class="row">
		<label for="ElementPMH_value">Phrase:</label>
		<?php echo CHtml::dropDownList('ElementPMH[phrase]', '', $model->getExamPhraseOptions(ExamPhrase::PART_PMH),
			array('onChange' => 'appendText($(this), $("#ElementPMH_value"));')); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'value'); ?>
		<?php echo $form->textArea($model,'value',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'value'); ?>
	</div>