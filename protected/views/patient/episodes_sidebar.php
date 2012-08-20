<div id="episodes_sidebar">
	<?php if (is_array($legacyepisodes)) foreach ($legacyepisodes as $i => $episode) {?>
	<div class="episode clearfix" style="display:block;border: 1px solid red;">
<div class="patient_actions">
		<span class="aBtn"><a class="episode showhide" href="#"><span
				class="hide"></span> </a> </span>
	</div>
		<div class="episode_nav" style="background-color:#eee">
			<div class="small"></div>
			<h4 style="color:#999;">Legacy events</h4>
			<ul class="events">
					<?php foreach ($episode->events as $event) {
						$highlight = false;

						if(isset($this->event) && $this->event->id == $event->id){
							$highlight = TRUE;
						}
						
						if ($event->eventType->class_name == 'OphTrOperation') {
							$event_path = Yii::app()->createUrl('patient/event').'/';
						} else {
							$event_path = Yii::app()->createUrl($event->eventType->class_name.'/Default/view').'/';
						}
						?>
						<li id="eventLi<?php echo $event->id ?>">
							<div class="quicklook" style="display: none; ">
								<span class="event"><?php echo $event->eventType->name?></span>
								<span class="info"><?php echo str_replace("\n","<br/>",$event->info)?></span>
								<?php if($event->hasIssue()) { ?>
									<span class="issue"><?php echo $event->getIssueText()?></span>
								<?php } ?>
							</div>
							<?php if($highlight) { ?>
							<div class="viewing">
							<?php } else { ?>
							<a style="color:#999;" href="<?php echo $event_path.$event->id?>" rel="<?php echo $event->id?>" class="show-event-details">
							<?php } ?>
									<span class="type<?php if($event->hasIssue()) { ?> statusflag<?php } ?>">
										<?php
										if ($event->eventType->class_name == 'OphTrOperation') {?>
											<img src="<?php echo Yii::app()->createUrl('img/_elements/icons/event/small/treatment_operation.png')?>" alt="op" width="19" height="19" />
										<?php } else {
											$assetpath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$event->eventType->class_name.'.img')).'/';?>
											<img src="<?php echo Yii::app()->createUrl($assetpath.'small.png')?>" alt="op" width="19" height="19" />
										<?php } ?>
									</span>
									<span class="date"> <?php echo $event->NHSDateAsHTML('datetime'); ?></span>
							<?php if(!$highlight) { ?>
							</a>
							<?php } else { ?>
							</div>
							<?php } ?>
						</li>
						<?php } ?>
				</ul>
			</ul>
		
		</div>
	</div>
	<?php } ?>
	<?php if (is_array($episodes)) foreach ($episodes as $i => $episode) {?>
		<div class="episode <?php echo empty($episode->end_date) ? 'closed' : 'open' ?> clearfix">
			<div class="episode_nav">
				<input type="hidden" name="episode-id" value="<?php echo $episode->id?>" />
				<div class="small"><?php echo $episode->NHSDate('start_date'); ?><span style="float:right;"><a href="<?php echo Yii::app()->createUrl('patient/episode/'.$episode->id)?>" rel="<?php echo $episode->id?>" class="episode-details"><?php if (@$current_episode && $current_episode->id == $episode->id){?><div class="summary"><?php }?>View summary<?php if (@$current_episode && $current_episode->id == $episode->id){?></div><?php }?></a></span></div>
				<h4><?php echo CHtml::encode($episode->firm->serviceSubspecialtyAssignment->subspecialty->name)?></h4>
				<ul class="events">
					<?php foreach ($episode->events as $event) {
						$highlight = false;

						if(isset($this->event) && $this->event->id == $event->id){
							$highlight = TRUE;
						}
						
						if ($event->eventType->class_name == 'OphTrOperation') {
							$event_path = Yii::app()->createUrl('patient/event').'/';
						} else {
							$event_path = Yii::app()->createUrl($event->eventType->class_name.'/default/view').'/';
						}
						?>
						<li id="eventLi<?php echo $event->id ?>">
							<div class="quicklook" style="display: none; ">
								<span class="event"><?php echo $event->eventType->name?></span>
								<span class="info"><?php echo str_replace("\n","<br/>",$event->info)?></span>
								<?php if($event->hasIssue()) { ?>
									<span class="issue"><?php echo $event->getIssueText()?></span>
								<?php } ?>
							</div>
							<?php if($highlight) { ?>
							<div class="viewing">
							<?php } else { ?>
							<a href="<?php echo $event_path.$event->id?>" rel="<?php echo $event->id?>" class="show-event-details">
							<?php } ?>
									<span class="type<?php if($event->hasIssue()) { ?> statusflag<?php } ?>">
										<?php
										if ($event->eventType->class_name == 'OphTrOperation') {?>
											<img src="<?php echo Yii::app()->createUrl('img/_elements/icons/event/small/treatment_operation.png')?>" alt="op" width="19" height="19" />
										<?php } else {
											$assetpath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$event->eventType->class_name.'.assets')).'/';?>
											<img src="<?php echo $assetpath.'img/small.png'?>" alt="op" width="19" height="19" />
										<?php } ?>
									</span>
									<span class="date"> <?php echo $event->NHSDateAsHTML('created_date'); ?></span>
							<?php if(!$highlight) { ?>
							</a>
							<?php } else { ?>
							</div>
							<?php } ?>
						</li>
						<?php } ?>
				</ul>
			</div>
			<div class="episode_details hidden" id="episode-details-<?php echo $episode->id?>">
				<div class="row"><span class="label">Start date:</span><?php echo $episode->NHSDate('start_date'); ?></div>
				<div class="row"><span class="label">End date:</span><?php echo ($episode->end_date ? $episode->NHSDate('end_date') : '-')?></div>
				<?php $has_diagnosis = $episode->hasPrincipalDiagnosis() ?>
				<div class="row"><span class="label">Principal eye:</span><?php echo ($has_diagnosis) ? $episode->getPrincipalDiagnosisEyeText() : 'No diagnosis' ?></div>
				<div class="row"><span class="label">Principal diagnosis:</span><?php echo ($has_diagnosis) ? $episode->getPrincipalDiagnosisDisorderTerm() : 'No diagnosis' ?></div>
				<div class="row"><span class="label">Subspecialty:</span><?php echo CHtml::encode($episode->firm->serviceSubspecialtyAssignment->subspecialty->name)?></div>
				<div class="row"><span class="label">Consultant firm:</span><?php echo CHtml::encode($episode->firm->name)?></div>
				<img class="folderIcon" src="<?php echo Yii::app()->createUrl('img/_elements/icons/folder_open.png')?>" alt="folder open" />
			</div>
		</div> <!-- .episode -->
	<?php }?>
</div> <!-- #episodes_sidebar -->
	<script type="text/javascript">
	// basic quicklook animation... 
	
			$(document).ready(function(){
				$('.quicklook').each(function(){
					var quick = $(this);
					var iconHover = $(this).parent().find('.type');
				iconHover.hover(function(e){
					quick.fadeIn('fast');
				},function(e){
					quick.fadeOut('fast');
				});	
				});
									
				}); // ready
	
	</script>
