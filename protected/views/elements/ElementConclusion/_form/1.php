Conclusion: <br />

	<div class="row">
		<label for="ElementPOH_value">Conclusion:</label>
		<?php echo CHtml::dropDownList('null', '', $model->getExamPhraseOptions(ExamPhrase::PART_CONCLUSION)); ?>
	</div>

	<div class="row">
		<label for="ElementPOH_value">Treatment:</label>
		<?php echo CHtml::dropDownList('null', '', $model->getExamPhraseOptions(ExamPhrase::PART_TREATMENT)); ?>
	</div>

	<div class="row">
		<label for="ElementPOH_value">Outcome:</label>
		<?php echo CHtml::dropDownList('null', '', $model->getExamPhraseOptions(ExamPhrase::PART_OUTCOME)); ?>
	</div>

	<div class="row">
		<label for="ElementPOH_value">Timing:</label>
		<?php echo CHtml::dropDownList('null', '', $model->getExamPhraseOptions(ExamPhrase::PART_TIMING)); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'value'); ?>
		<?php echo $form->textArea($model,'value',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'value'); ?>
	</div>