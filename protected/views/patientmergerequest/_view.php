<?php
/* @var $this PatientMergeRequestController */
/* @var $data PatientMergeRequest */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id' => $data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('primary_id')); ?>:</b>
	<?php echo CHtml::encode($data->primary_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('primary_hos_num')); ?>:</b>
	<?php echo CHtml::encode($data->primary_hos_num); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('primary_nhsnum')); ?>:</b>
	<?php echo CHtml::encode($data->primary_nhsnum); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('primary_dob')); ?>:</b>
	<?php echo CHtml::encode($data->primary_dob); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('primary_gender')); ?>:</b>
	<?php echo CHtml::encode($data->primary_gender); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('secondary_id')); ?>:</b>
	<?php echo CHtml::encode($data->secondary_id); ?>
	<br />

</div>