<div class="column event
	<?php echo $this->moduleNameCssClass;?>
	<?php echo $this->moduleStateCssClass; ?>
	" style="margin-left:14.6%">

	<header class="event-header">
		<?php $this->renderPartial('//patient/event_tabs'); ?>
		<?php $this->renderIndexSearch(); ?>
		<?php $this->renderPartial('//patient/event_actions'); ?>
	</header>
    <div class="event-content <?=($this->event->is_automated) ? 'auto' : ''?>" id="event-content">

		<h2 class="event-title <?=($this->event->is_automated) ? 'auto' : ''?>" style="background-image: url('<?=$this->event->getEventIcon('medium')?>');"><?php echo $this->title?> <?php $this->renderPartial('//patient/event_automated'); ?></h2>

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
    $this->renderEventMetadata();
} ?>
	</div>
</div>
