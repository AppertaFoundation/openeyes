<div class="view">
	<strong>Eye(s) to be operated on:</strong>
	<?php echo CHtml::encode($operation->getEyeText()); ?>
</div>
<div class="view">
	<strong>Procedure(s) entered:</strong>
<?php
	$procedures = '';
	if (!empty($operation->procedures)) {
		foreach ($operation->procedures as $procedure) {
			$procedures .= $procedure->term;
			$procedures .= ', ';
		}
		$procedures = substr($procedures, 0, strlen($procedures) - 2);
	}
	echo $procedures; ?>
</div>
<div class="view">
	<strong>Consultant required?</strong>
	<?php echo CHtml::encode($operation->getBooleanText('consultant_required')); ?>
</div>
<div class="view">
	<strong>Anaesthetic required:</strong>
	<?php echo CHtml::encode($operation->getAnaestheticText()); ?>
</div>
<div class="view">
	<strong>Overnight stay required?</strong>
	<?php echo CHtml::encode($operation->getBooleanText('overnight_stay')); ?>
</div>
<div class="view">
	<strong>Comments:</strong>
	<?php echo CHtml::encode($operation->comments); ?>
</div>