<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('disorder_id')); ?>:</b>
	<?php echo CHtml::encode($data->disorder->term); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('specialty_id')); ?>:</b>
	<?php echo CHtml::encode($data->specialty->name); ?>
	<br />


</div>