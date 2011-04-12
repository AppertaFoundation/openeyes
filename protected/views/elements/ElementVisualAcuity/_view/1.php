<br />
Visual Acuity:

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('rva_ua')); ?>:</b>
	<?php echo CHtml::encode($data->getVisualAcuityText(ElementVisualAcuity::SNELLEN_METRE, 'rva_ua')); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('rva_ph')); ?>:</b>
	<?php echo CHtml::encode($data->getVisualAcuityText(ElementVisualAcuity::SNELLEN_METRE, 'rva_ph')); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('rva_cr')); ?>:</b>
	<?php echo CHtml::encode($data->getVisualAcuityText(ElementVisualAcuity::SNELLEN_METRE, 'rva_cr')); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('right_aid')); ?>:</b>
	<?php echo CHtml::encode($data->getAidText('right_aid')); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('lva_ua')); ?>:</b>
	<?php echo CHtml::encode($data->getVisualAcuityText(ElementVisualAcuity::SNELLEN_METRE, 'lva_ua')); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('lva_ph')); ?>:</b>
	<?php echo CHtml::encode($data->getVisualAcuityText(ElementVisualAcuity::SNELLEN_METRE, 'lva_ph')); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('lva_cr')); ?>:</b>
	<?php echo CHtml::encode($data->getVisualAcuityText(ElementVisualAcuity::SNELLEN_METRE, 'lva_cr')); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('left_aid')); ?>:</b>
	<?php echo CHtml::encode($data->getAidText('left_aid')); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('distance')); ?>:</b>
	<?php echo CHtml::encode($data->getDistanceText(ElementVisualAcuity::SNELLEN_METRE)); ?> metres
	<br />

</div>