<br />
Posterior segment:

<div class="view">
	<?php EyeDrawService::activeEyeDrawField($data, 'left', false);?>
	<b><?php echo CHtml::encode($data->getAttributeLabel('description_left')); ?>:</b>
	<?php echo CHtml::encode($data->description_left); ?>
	<br />

	<?php EyeDrawService::activeEyeDrawField($data, 'right', false);?>
	<b><?php echo CHtml::encode($data->getAttributeLabel('description_right')); ?>:</b>
	<?php echo CHtml::encode($data->description_right); ?>
	<br />
</div>
