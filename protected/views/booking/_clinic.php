<?php
$firm = $operation->event->episode->firm; ?>
<div class="view">
	<strong>Service:</strong>
	<?php echo CHtml::encode($firm->serviceSpecialtyAssignment->service->name); ?>
</div>
<div class="view">
	<strong>Firm:</strong>
	<?php echo CHtml::encode($firm->name); ?>
</div>
<div class="view">
	<strong>Referral date:</strong>
</div>
<div class="view">
	<strong>Clinic date:</strong>
</div>
<div class="view">
	<strong>PCT Clinical pathway:</strong>
</div>
<div class="view">
	<strong>Diagnosis:</strong>
</div>