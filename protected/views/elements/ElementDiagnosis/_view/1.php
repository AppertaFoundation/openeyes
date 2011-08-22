<h3>Diagnosis</h3>

<div class="view">
	<strong><?php echo $data->getAttributeLabel('eye'); ?></strong>
		<?php echo $data->getEyeText(); ?>
	<br />
	<strong><?php echo CHtml::encode($data->getAttributeLabel('disorder_id')); ?>:</strong>
	<?php echo CHtml::encode($data->disorder->term); ?>
</div>