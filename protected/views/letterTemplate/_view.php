<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('phrase')); ?>:</b>
	<?php echo CHtml::encode($data->phrase); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('specialty_id')); ?>:</b>
	<?php echo CHtml::encode($data->getSpecialtyText()); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('to')); ?>:</b>
	<?php echo CHtml::encode($data->getToText()); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('cc')); ?>:</b>
	<?php echo CHtml::encode($data->getCcText()); ?>
	<br />


</div>
