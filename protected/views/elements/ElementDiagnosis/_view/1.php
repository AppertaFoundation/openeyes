<h3>Diagnosis</h3>

<div class="view">
	<b><?php echo CHtml::encode($data->getAttributeLabel('disorder_id')); ?>:</b>
	<?php echo CHtml::encode($data->disorder->term . ' - ' . $data->disorder->fully_specified_name); ?>
	<br />

	<b><?php echo $data->getAttributeLabel('eye'); ?></b>
		<?php echo $data->getEyeText(); ?>

</div>
