Posterior segment: <br />

	<?php echo $form->errorSummary($model); ?>

	<?php echo EyeDrawService::activeEyeDrawField($this, $model, 'left');?>
	<p>
	<label for="ElementPosteriorSegment_description_left"><?php echo CHtml::encode($model->getAttributeLabel('description_left')); ?></label><br />
	<?php echo $form->textArea($model, 'description_left', array('rows'=>15, 'cols'=>75)); ?>
	<?php echo $form->error($model,'description_left'); ?> <br />
	</p>

	<?php echo EyeDrawService::activeEyeDrawField($this, $model, 'right');?>
	<p>
	<label for="ElementPosteriorSegment_description_right"><?php echo CHtml::encode($model->getAttributeLabel('description_right')); ?></label><br />
	<?php echo $form->textArea($model, 'description_right', array('rows'=>15, 'cols'=>75)); ?>
	<?php echo $form->error($model,'description_right'); ?> <br />
	</p>

