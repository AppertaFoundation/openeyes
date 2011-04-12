HPC: <br />

	<div class="row">
		<label for="ElementHPC_value">History:</label>
		<?php echo CHtml::dropDownList('ElementHPC[phrase]', '', $model->getExamPhraseOptions(ExamPhrase::PART_HISTORY),
			array('onChange' => 'appendText($(this), $("#ElementHPC_value"));')); ?>
	</div>

	<div class="row">
		<label for="ElementHPC_value">Severity:</label>
		<?php echo CHtml::dropDownList('ElementHPC[phrase]', '', $model->getExamPhraseOptions(ExamPhrase::PART_SEVERITY),
			array('onChange' => 'appendText($(this), $("#ElementHPC_value"));')); ?>
	</div>

	<div class="row">
		<label for="ElementHPC_value">Onset:</label>
		<?php echo CHtml::dropDownList('ElementHPC[phrase]', '', $model->getExamPhraseOptions(ExamPhrase::PART_ONSET),
			array('onChange' => 'appendText($(this), $("#ElementHPC_value"));')); ?>
	</div>

	<div class="row">
		<label for="ElementHPC_value">Site:</label>
		<?php echo CHtml::dropDownList('ElementHPC[phrase]', '', $model->getExamPhraseOptions(ExamPhrase::PART_SITE),
			array('onChange' => 'appendText($(this), $("#ElementHPC_value"));')); ?>
	</div>

	<div class="row">
		<label for="ElementHPC_value">Duration:</label>
		<?php echo CHtml::dropDownList('ElementHPC[phrase]', '', $model->getExamPhraseOptions(ExamPhrase::PART_DURATION),
			array('onChange' => 'appendText($(this), $("#ElementHPC_value"));')); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'value'); ?>
		<?php echo $form->textArea($model,'value',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'value'); ?>
	</div>