<h2><?php echo $element->elementType->name ?></h2>
<div class="details">
	<div class="eventDetail aligned">
		<div class="label"><?php echo CHtml::encode($element->getAttributeLabel('injection_status_id'))?>:</div>
		<div class="data"><?php echo $element->injection_status ?></div>
	</div>
	<?php if ($element->injection_status && $element->injection_status->deferred) { ?>
	<div class="eventDetail aligned">
		<div class="label"><?php echo CHtml::encode($element->getAttributeLabel('injection_deferralreason_id'))?>:</div>
		<div class="data"><?php echo $element->getInjectionDeferralReason() ?></div>
	</div>
	<?php } ?>
</div>
