<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b>Service:</b>
	<?php echo CHtml::encode($data->getServiceText()); ?>
	<br />

	<b>Specialty:</b>
	<?php echo CHtml::encode($data->getSpecialtyText()); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('pas_code')); ?>:</b>
	<?php echo CHtml::encode($data->pas_code); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->name); ?>
	<br />


</div>