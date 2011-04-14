SOCIAL HISTORY: <br />

	<div class="row">
		<label for="ElementSocialHistory_value">Phrase:</label>
		<?php echo CHtml::dropDownList('ElementSocialHistory[phrase]', '', $model->getPhraseBySpecialtyOptions('Social history'),
			array('onChange' => 'appendText($(this), $("#ElementSocialHistory_value"));')); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'value'); ?>
		<?php echo $form->textArea($model,'value',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'value'); ?>
	</div>
