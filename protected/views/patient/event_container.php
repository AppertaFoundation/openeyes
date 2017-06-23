<div class="row messages patient fixed">
<?php $this->renderPartial('//patient/_patient_alerts')?>
</div>
<div class="box content row">

	<?php if ($this->patient->isDeceased()) {?>
		<div id="deceased-notice" class="alert-box alert with-icon">
			This patient is deceased (<?php echo $this->patient->NHSDate('date_of_death'); ?>)
		</div>
	<?php }?>


    <?php $this->renderSidebar('//patient/episodes_sidebar') ?>
    <?php $this->renderPartial('//patient/event_content', array(
        'content' => $content,
    )); ?>
</div>
