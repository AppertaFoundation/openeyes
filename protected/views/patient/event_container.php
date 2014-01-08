<h1 class="badge">Episodes and events</h1>

<div class="box content">
	<div class="row">

		<?php if ($this->patient->isDeceased()) {?>
			<div id="deceased-notice" class="alert-box alert with-icon">
				This patient is deceased (<?php echo $this->patient->NHSDate('date_of_death'); ?>)
			</div>
		<?php }?>

		<?php $this->renderPartial('//patient/episodes_sidebar');?>

		<div class="large-10 column event <?php echo $this->moduleNameCssClass;?> <?php echo $this->moduleStateCssClass; ?>">

			<?php $this->renderPartial('//patient/event_tabs')?>

			<div class="event-content" id="event-content">

				<h2 class="event-title"><?php echo $this->title?></h2>

				<?php $this->renderPartial('//base/_messages'); ?>

					<?php echo $content; ?>
					<?php if ($this->action->id == 'view') {
						$this->renderEventMetadata();
					} ?>
			</div>
		</div>
	</div>
</div>
