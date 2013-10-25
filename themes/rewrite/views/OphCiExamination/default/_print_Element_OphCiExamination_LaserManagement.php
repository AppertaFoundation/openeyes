<h2><?php echo $element->elementType->name ?></h2>
<div class="details">
	<div class="eventDetail aligned">
		<div class="label"><?php echo CHtml::encode($element->getAttributeLabel('laser_status_id'))?>:</div>
		<div class="data"><?php echo $element->laser_status ?></div>
	</div>
	<?php if ($element->laser_status && $element->laser_status->deferred) { ?>
	<div class="eventDetail aligned">
		<div class="label"><?php echo CHtml::encode($element->getAttributeLabel('laser_deferralreason_id'))?>:</div>
		<div class="data"><?php echo $element->getLaserDeferralReason() ?></div>
	</div>
	<?php } else if ($element->laser_status->book || $element->laser_status->event) { ?>

		<div class="cols2 clearfix">
			<div class="left eventDetail">
				<?php if ($element->hasRight()) {
					$this->renderPartial('_view_' . get_class($element) . '_fields',
						array('side' => 'right', 'element' => $element));
				} else { ?>
					Not recorded
				<?php } ?>
			</div>
			<div class="right eventDetail">
				<?php if ($element->hasLeft()) {
					$this->renderPartial('_view_' . get_class($element) . '_fields',
						array('side' => 'left', 'element' => $element));
				} else { ?>
					Not recorded
				<?php } ?>
			</div>
		</div>
	<?php } ?>
</div>
