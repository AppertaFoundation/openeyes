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

	<?php /*
    <b><?php echo CHtml::encode($data->getAttributeLabel('secondary_hos_num')); ?>:</b>
    <?php echo CHtml::encode($data->secondary_hos_num); ?>
    <br />

    <b><?php echo CHtml::encode($data->getAttributeLabel('secondary_nhsnum')); ?>:</b>
    <?php echo CHtml::encode($data->secondary_nhsnum); ?>
    <br />

    <b><?php echo CHtml::encode($data->getAttributeLabel('secondary_dob')); ?>:</b>
    <?php echo CHtml::encode($data->secondary_dob); ?>
    <br />

    <b><?php echo CHtml::encode($data->getAttributeLabel('secondary_gender')); ?>:</b>
    <?php echo CHtml::encode($data->secondary_gender); ?>
    <br />

    <b><?php echo CHtml::encode($data->getAttributeLabel('merge_json')); ?>:</b>
    <?php echo CHtml::encode($data->merge_json); ?>
    <br />

    <b><?php echo CHtml::encode($data->getAttributeLabel('comment')); ?>:</b>
    <?php echo CHtml::encode($data->comment); ?>
    <br />

    <b><?php echo CHtml::encode($data->getAttributeLabel('status')); ?>:</b>
    <?php echo CHtml::encode($data->status); ?>
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

    */ ?>

</div>