<?php

extract($this->getEpisodes());

if ($module = $this->getModule()) {
	$module = $module->getName();
	if (file_exists(Yii::getPathOfAlias('application.modules.'.$module.'.assets'))) {
		Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$module.'.assets'),true).'/';
	}
}
?>

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

			<?php $this->renderPartial('//patient/event_tabs',array('hidden'=>(boolean) (count($ordered_episodes)<1 && count($supportserviceepisodes) <1 && count($legacyepisodes) <1)))?>

			<div class="event-content">
					<?php echo $content; ?>
					<?php if ($this->action->id == 'view') {
						$this->renderEventMetadata();
					} ?>
			</div>
		</div>
	</div>
</div>