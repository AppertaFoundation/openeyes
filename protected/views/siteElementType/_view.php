<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('possible_element_type_id')); ?>:</b>
	<?php echo CHtml::encode($data->possible_element_type_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('specialty_id')); ?>:</b>
	<?php echo CHtml::encode($data->specialty_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('view_number')); ?>:</b>
	<?php echo CHtml::encode($data->view_number); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('required')); ?>:</b>
	<?php echo CHtml::encode($data->required); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('first_in_episode')); ?>:</b>
	<?php echo CHtml::encode($data->first_in_episode); ?>
	<br />


</div>