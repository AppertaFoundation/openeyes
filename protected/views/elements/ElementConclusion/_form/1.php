Conclusion: <br />

	<div class="row">
		<label for="ElementConclusion_value">Conclusion:</label>
		<?php echo CHtml::dropDownList('ElementConclusion[phrase]', '', $model->getExamPhraseOptions(ExamPhrase::PART_CONCLUSION),
			array('onChange' => 'appendText($(this), $("#ElementConclusion_value"));')); ?>
	</div>

	<div class="row">
		<label for="ElementConclusion_value">Treatment:</label>
		<?php echo CHtml::dropDownList('ElementConclusion[phrase]', '', $model->getExamPhraseOptions(ExamPhrase::PART_TREATMENT),
			array('onChange' => 'appendText($(this), $("#ElementConclusion_value"));')); ?>
	</div>

	<div class="row">
		<label for="ElementConclusion_value">Outcome:</label>
		<?php echo CHtml::dropDownList('ElementConclusion[phrase]', '', $model->getExamPhraseOptions(ExamPhrase::PART_OUTCOME),
			array('onChange' => 'appendText($(this), $("#ElementConclusion_value"));')); ?>
	</div>

	<div class="row">
		<label for="ElementConclusion_value">Timing:</label>
		<?php echo CHtml::dropDownList('ElementConclusion[phrase]', '', $model->getExamPhraseOptions(ExamPhrase::PART_TIMING),
			array('onChange' => 'appendText($(this), $("#ElementConclusion_value"));')); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'value'); ?>
		<?php echo $form->textArea($model,'value',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'value'); ?>
	</div>