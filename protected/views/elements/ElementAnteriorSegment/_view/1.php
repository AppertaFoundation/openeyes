<br />
Anterior segment:

<div class="view">
	<?= EyeDrawService::activeEyeDrawField($data, 'left', false);?>
	<b><?php echo CHtml::encode($data->getAttributeLabel('description_left')); ?>:</b>
	<?php echo CHtml::encode($data->description_left); ?>
	<br />

	<?= EyeDrawService::activeEyeDrawField($data, 'right', false);?>
	<b><?php echo CHtml::encode($data->getAttributeLabel('description_right')); ?>:</b>
	<?php echo CHtml::encode($data->description_right); ?>
	<br />
</div>
