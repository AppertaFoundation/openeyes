HPC: <br />

	<div class="row">
		<label for="ElementPOH_value">History:</label>
		<?php echo CHtml::dropDownList('null', '', $model->getExamPhraseOptions(ExamPhrase::PART_HISTORY)); ?>
	</div>

	<div class="row">
		<label for="ElementPOH_value">Severity:</label>
		<?php echo CHtml::dropDownList('null', '', $model->getExamPhraseOptions(ExamPhrase::PART_SEVERITY)); ?>
	</div>

	<div class="row">
		<label for="ElementPOH_value">Onset:</label>
		<?php echo CHtml::dropDownList('null', '', $model->getExamPhraseOptions(ExamPhrase::PART_ONSET)); ?>
	</div>

	<div class="row">
		<label for="ElementPOH_value">Site:</label>
		<?php echo CHtml::dropDownList('null', '', $model->getExamPhraseOptions(ExamPhrase::PART_SITE)); ?>
	</div>

	<div class="row">
		<label for="ElementPOH_value">Duration:</label>
		<?php echo CHtml::dropDownList('null', '', $model->getExamPhraseOptions(ExamPhrase::PART_DURATION)); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'value'); ?>
		<?php echo $form->textArea($model,'value',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'value'); ?>
	</div>