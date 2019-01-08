<?php
/* @var $this DisorderController */
/* @var $data Disorder */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('fully_specified_name')); ?>:</b>
	<?php echo CHtml::encode($data->fully_specified_name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('term')); ?>:</b>
	<?php echo CHtml::encode($data->term); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('last_modified_user_id')); ?>:</b>
	<?php echo CHtml::encode($data->last_modified_user_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('last_modified_date')); ?>:</b>
	<?php echo CHtml::encode($data->last_modified_date); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('created_user_id')); ?>:</b>
	<?php echo CHtml::encode($data->created_user_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('created_date')); ?>:</b>
	<?php echo CHtml::encode($data->created_date); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('specialty_id')); ?>:</b>
	<?php echo CHtml::encode($data->specialty_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('active')); ?>:</b>
	<?php echo CHtml::encode($data->active); ?>
	<br />

	*/ ?>

</div>