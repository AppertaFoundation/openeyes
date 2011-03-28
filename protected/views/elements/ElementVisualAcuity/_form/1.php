Visual Acuity:<br />

	<?php
		$visualAcuityOptions = $model->getVisualAcuityOptions(ElementVisualAcuity::SNELLEN_METRE);
		$aidOptions = $model->getAidOptions(ElementVisualAcuity::SNELLEN_METRE);
	?>

	<?php echo $form->errorSummary($model); ?>

	<label for="ElementVisualAcuity_rva_ua"><?php echo CHtml::encode($model->getAttributeLabel('rva_ua')); ?></label>
	<?php echo $form->dropDownList($model, 'rva_ua', $visualAcuityOptions) ?>
	<?php echo $form->error($model,'rva_ua'); ?>

	<label for="ElementVisualAcuity_rva_ph"><?php echo CHtml::encode($model->getAttributeLabel('rva_ph')); ?></label>
	<?php echo $form->dropDownList($model, 'rva_ph', $visualAcuityOptions) ?>
	<?php echo $form->error($model,'rva_ph'); ?>

	<label for="ElementVisualAcuity_rva_cr"><?php echo CHtml::encode($model->getAttributeLabel('rva_cr')); ?></label>
	<?php echo $form->dropDownList($model, 'rva_cr', $visualAcuityOptions) ?>
	<?php echo $form->error($model,'rva_cr'); ?>

	<label for="ElementVisualAcuity_right_aid"><?php echo CHtml::encode($model->getAttributeLabel('right_aid')); ?></label>
	<?php echo $form->dropDownList($model, 'right_aid', $aidOptions) ?>
	<?php echo $form->error($model,'right_aid'); ?>
<br />
	<label for="ElementVisualAcuity_lva_ua"><?php echo CHtml::encode($model->getAttributeLabel('lva_ua')); ?></label>
	<?php echo $form->dropDownList($model, 'lva_ua', $visualAcuityOptions) ?>
	<?php echo $form->error($model,'lva_ua'); ?>

	<label for="ElementVisualAcuity_lva_ph"><?php echo CHtml::encode($model->getAttributeLabel('lva_ph')); ?></label>
	<?php echo $form->dropDownList($model, 'lva_ph', $visualAcuityOptions) ?>
	<?php echo $form->error($model,'lva_ph'); ?>

	<label for="ElementVisualAcuity_lva_cr"><?php echo CHtml::encode($model->getAttributeLabel('lva_cr')); ?></label>
	<?php echo $form->dropDownList($model, 'lva_cr', $visualAcuityOptions) ?>
	<?php echo $form->error($model,'lva_cr'); ?>

	<label for="ElementVisualAcuity_left_aid"><?php echo CHtml::encode($model->getAttributeLabel('left_aid')); ?></label>
	<?php echo $form->dropDownList($model, 'left_aid', $aidOptions) ?>
	<?php echo $form->error($model,'left_aid'); ?>

	<div class="row">
		<label for="ElementVisualAcuity_distance"><?php echo CHtml::encode($model->getAttributeLabel('distance')); ?></label>
		<?php echo  $form->dropDownList($model, 'distance', $model->getDistanceOptions(ElementVisualAcuity::SNELLEN_METRE)); ?>
	</div>
