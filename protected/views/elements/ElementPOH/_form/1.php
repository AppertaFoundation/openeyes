POH: <br />

	<div class="row">
		<label for="ElementPOH_value">Phrase:</label>
		<?php echo CHtml::dropDownList('null', '', $model->getExamPhraseOptions(ExamPhrase::PART_POH)); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'value'); ?>
		<?php echo $form->textArea($model,'value',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'value'); ?>
	</div>