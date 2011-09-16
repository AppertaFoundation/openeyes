<?php

Yii::app()->clientScript->scriptMap['jquery.js'] = false;
$firm = $operation->event->episode->firm; ?>
<div class="view">
	<strong>Service:</strong>
	<?php echo CHtml::encode($firm->serviceSpecialtyAssignment->service->name); ?>
</div>
<div class="view">
	<strong>Firm:</strong>
	<?php echo CHtml::encode($firm->name); ?>
</div>
<?php
if (!empty($operation->booking)) {
	$theatre = $operation->booking->session->sequence->theatre; ?>
<div class="view">
	<strong>Location:</strong>
	<?php echo CHtml::encode($theatre->site->name) . ' - ' . 
		CHtml::encode($theatre->name); ?>
</div>
<?php	
} ?>
<!--div class="view">
	<strong>Referral date:</strong>
</div-->
<!--div class="view">
	<strong>Clinic date:</strong>
</div>
<div class="view">
	<strong>PCT Clinical pathway:</strong>
</div>
<div class="view">
	<strong>Diagnosis:</strong>
</div-->
