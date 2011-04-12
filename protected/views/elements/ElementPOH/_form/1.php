POH: <br />

	<div class="row">
		<label for="ElementPOH_phrase">Phrase:</label>
		<?php echo CHtml::dropDownList('ElementPOH[phrase]', '', $model->getExamPhraseOptions(ExamPhrase::PART_POH),
			array('onChange' => 'appendText($(this), $("#ElementPOH_value"));')); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'value'); ?>
		<?php echo $form->textArea($model,'value',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'value'); ?>
	</div>