<div class="column event
	<?php echo $this->moduleNameCssClass;?>
	<?php echo $this->moduleStateCssClass; ?>
">

	<nav class="event-header">
		<?php $this->renderPartial('//patient/event_tabs'); ?>
		<?php $this->renderPartial('//patient/event_actions'); ?>
	</nav>

	<div class="event-content <?=($this->event->is_automated) ? 'auto' : ''?>" id="event-content">

		<h2 class="event-title <?=($this->event->is_automated) ? 'auto' : ''?>"><?php echo $this->title?> <?php $this->renderPartial('//patient/event_automated'); ?></h2>

		<?php $this->renderPartial('//base/_messages'); ?>

		<?php if($this->action->id == 'view' && $this->event->isEventDateDifferentFromCreated()){?>
			<div class="row data-row">
				<div class="large-2 column" style="margin-left: 10px;">
					<div class="data-label"><?php echo $this->event->getAttributeLabel('event_date') ?>:</div>
				</div>
				<div class="large-9 column end">
					<div class="data-value"><?php echo $this->event->NHSDate('event_date') ?></div>
				</div>
			</div>
		<?php } ?>

		<?php echo $content; ?>

		<?php if ($this->action->id == 'view') {
    $this->renderPartial('event_metadata', ['Element'=>$Element]);
} ?>
	</div>
</div>