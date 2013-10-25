	<div class="eventDetail aligned">
		<div class="label"><?php echo $element->getAttributeLabel($side . '_clinicalret_id') ?>:</div>
		<div class="data"><?php echo $element[$side . '_clinicalret']->name ?></div>
	</div>
	<div class="eventDetail aligned">
		<div class="label"><?php echo $element->getAttributeLabel($side . '_nscretinopathy_id') ?>:</div>
		<div class="data"><?php echo $element[$side . '_nscretinopathy']->name ?></div>
	</div>
	<div class="eventDetail aligned">
		<div class="label"><?php echo $element->getAttributeLabel($side . '_nscretinopathy_photocoagulation') ?>:</div>
		<div class="data"><?php echo ($element[$side . '_nscretinopathy_photocoagulation']) ? "Yes" : "No" ?></div>
	</div>
	<?php if ($element->{$side . '_clinicalmac'}) {
	?>
	<div class="eventDetail aligned">
		<div class="label"><?php echo $element->getAttributeLabel($side . '_clinicalmac_id') ?>:</div>
		<div class="data"><?php echo $element->{$side . '_clinicalmac'}->name ?></div>
	</div>
	<?php
	}
	?>
	<div class="eventDetail aligned">
		<div class="label"><?php echo $element->getAttributeLabel($side . '_nscmaculopathy_id') ?>:</div>
		<div class="data"><?php echo $element[$side . '_nscmaculopathy']->name ?></div>
	</div>
	<div class="eventDetail aligned">
		<div class="label"><?php echo $element->getAttributeLabel($side . '_nscmaculopathy_photocoagulation') ?>:</div>
		<div class="data"><?php echo ($element[$side . '_nscmaculopathy_photocoagulation']) ? "Yes" : "No" ?></div>
	</div>
