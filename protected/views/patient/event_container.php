<h1 class="badge">Episodes and events</h1>

<div class="box content row">

	<?php if ($this->patient->isDeceased()) {?>
		<div id="deceased-notice" class="alert-box alert with-icon">
			This patient is deceased (<?php echo $this->patient->NHSDate('date_of_death'); ?>)
		</div>
	<?php }?>

	<?php $this->renderPartial('//patient/episodes_sidebar');?>
	<?php $this->renderPartial('//patient/event_content', array(
		'content' => $content
	)); ?>
</div>
