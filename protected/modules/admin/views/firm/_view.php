<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('service_id')); ?>:</b>
	<?php echo CHtml::encode($data->service->name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('specialty_id')); ?>:</b>
	<?php echo CHtml::encode($data->specialty->name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('pas_code')); ?>:</b>
	<?php echo CHtml::encode($data->pas_code); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('contact_id')); ?>:</b>
	<?php echo CHtml::encode($data->contact->nick_name); ?>
	<br />


</div>