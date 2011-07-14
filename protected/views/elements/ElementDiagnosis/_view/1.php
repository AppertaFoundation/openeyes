<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('disorder_id')); ?>:</b>
	<?php echo CHtml::encode($data->disorder->term . ' - ' . $data->disorder->fully_specified_name); ?>
	<br />

	<div class="view">
		<strong><?php echo $data->getAttributeLabel('eye'); ?></strong>
		<?php echo $data->getEyeText(); ?>
	</div>

</div>
