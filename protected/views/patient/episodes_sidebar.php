<div id="episodes_sidebar">
	<?php
	$legacyletters = EventType::model()->find('class_name=?',array('OphLeEpatientletter'));
	?>
	<?php if ($legacyletters && !$legacyletters->disabled && is_array($legacyepisodes)) foreach ($legacyepisodes as $i => $episode) {?>
	<div class="episode open clearfix" style="display:block;">
		<div class="episode_nav legacy">
			<div class="start_date small">
				<?php echo $episode->NHSDate('start_date')?>
				<span class="aBtn">
					<a class="sprite showhide2 legacy" href="#">
						<span class="<?php if ((!$this->event || $this->event->eventType->class_name != 'OphLeEpatientletter') && !@Yii::app()->session['episode_hide_status']['legacy']) {?>show<?php }else{?>hide<?php }?>"></span>
					</a>
				</span>
			</div>
			<h4 class="legacy" style="margin-left: 8px;">Legacy events</h4>
			<ul class="events"<?php if ((!$this->event || $this->event->eventType->class_name != 'OphLeEpatientletter') && !@Yii::app()->session['episode_hide_status']['legacy']) {?> style="display: none;"<?php }?>>
				<?php foreach ($episode->events as $event) {
					$highlight = false;

					if(isset($this->event) && $this->event->id == $event->id){
						$highlight = TRUE;
					}
					
					$event_path = Yii::app()->createUrl($event->eventType->class_name.'/Default/view').'/';
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
								<?php $assetpath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$event->eventType->class_name.'.assets')).'/';?>
								<img src="<?php echo Yii::app()->createUrl($assetpath.'img/small.png')?>" alt="op" width="19" height="19" />
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
		</div>
	</div>
	<?php } ?>
	<?php if (is_array($ordered_episodes)) 
		foreach ($ordered_episodes as $specialty_episodes) {?>
			<div class="specialty"><?php echo $specialty_episodes['specialty']->name ?></div>
			
			<?php foreach ($specialty_episodes['episodes'] as $i => $episode) {?>
			<div class="episode <?php echo empty($episode->end_date) ? 'closed' : 'open' ?> clearfix">
				<div class="episode_nav">
					<input type="hidden" name="episode-id" value="<?php echo $episode->id?>" />
					<div class="start_date small">
						<?php echo $episode->NHSDate('start_date')?>
						<span class="aBtn">
							<a class="sprite showhide2" href="#">
								<span class="<?php if ((!@$current_episode || $current_episode->id != $episode->id) && $episode->hidden) {?>show<?php }else{?>hide<?php }?>"></span>
							</a>
						</span>
					</div>
					<h4><?php echo CHtml::link(CHtml::encode($episode->firm->serviceSubspecialtyAssignment->subspecialty->name),array('/patient/episode/'.$episode->id),array('class'=>'title_summary'.((!$this->event && @$current_episode && $current_episode->id == $episode->id) ? ' viewing' : '')))?></h4>
					<ul	<?php if ($episode->hidden) { ?>class="events show" style="display: none;"<?php } else { ?>class="events hide"<?php }?>>
						<?php foreach ($episode->events as $event) {
							$highlight = false;
	
							if(isset($this->event) && $this->event->id == $event->id){
								$highlight = TRUE;
							}
							
							$event_path = Yii::app()->createUrl($event->eventType->class_name.'/default/view').'/';
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
											<?php if (file_exists(Yii::getPathOfAlias('application.modules.'.$event->eventType->class_name.'.assets'))) {
												$assetpath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$event->eventType->class_name.'.assets')).'/';
											} else {
												$assetpath = '/assets/';
											}?>
											<img src="<?php echo $assetpath.'img/small.png'?>" alt="op" width="19" height="19" />
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
					<div class="row"><span class="label">Principal eye:</span><?php echo ($episode->diagnosis) ? ($episode->eye ? $episode->eye->name : 'None') : 'No diagnosis' ?></div>
					<div class="row"><span class="label">Principal diagnosis:</span><?php echo ($episode->diagnosis) ? ($episode->diagnosis ? $episode->diagnosis->term : 'none') : 'No diagnosis' ?></div>
					<div class="row"><span class="label">Subspecialty:</span><?php echo CHtml::encode($episode->firm->serviceSubspecialtyAssignment->subspecialty->name)?></div>
					<div class="row"><span class="label">Consultant firm:</span><?php echo CHtml::encode($episode->firm->name)?></div>
					<img class="folderIcon" src="<?php echo Yii::app()->createUrl('img/_elements/icons/folder_open.png')?>" alt="folder open" />
				</div>
			</div> <!-- .episode -->
		<?php }?>
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
