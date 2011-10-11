<?php
$cs = Yii::app()->getClientScript();
$cs->registerCoreScript('jquery');
$cs->registerCoreScript('jquery.ui');
?>
		<h2>Episodes &amp; Events</h2>
		<div class="fullWidth fullBox clearfix">
			<div id="episodesBanner whiteBox">
				<form><button type="submit" value="submit" class="btn_newEvent ir" id="addNewEvent">Add New Event</button></form>
				<p><img style="position:relative; top:-2px;"src="/img/_elements/icons/event_blank.png" alt="event_blank" width="16" height="16" /> <strong>&nbsp;Event: HPC</strong></p>
			</div>
			<div id="episodes_sidebar">
				<?php foreach ($episodes as $i => $episode) {
					if ($i == 0) $current_episode = $episode;
					?>
					<div class="episode open clearfix">
						<div class="episode_nav">
							<input type="hidden" name="episode-id" value="<?php echo $episode->id?>" />
							<div class="small"><?php echo date('d.m.Y',strtotime($episode->start_date))?><span style="float:right;"><a href="#" rel="<?php echo $episode->id?>" class="episode-details">Details</a><span></div>
							<h4><?php echo CHtml::encode($episode->firm->serviceSpecialtyAssignment->specialty->name)?></h4>
							<ul class="events">
								<?php foreach ($episode->events as $event) {?>
									<?php
									$elements = $this->service->getElements(
										null, null, null, 0, $event
									);
									$scheduled = false;
									foreach ($elements as $element) {
										if (get_class($element) == 'ElementOperation' && $element->status == ElementOperation::STATUS_SCHEDULED) {
											$scheduled = true;
										}
									}
									?>
									<li><a href="#" rel="<?php echo $event->id?>" class="show-event-details"><span class="type"><img src="/img/_elements/icons/event_op_<?php if (!$scheduled) echo 'un'?>scheduled.png" alt="op" width="16" height="16" /></span><span class="date"> <?php echo date('d/m/Y',strtotime($event->datetime))?></span></a></li>
								<?php }?>
							</ul>
						</div>
						<div class="episode_details hidden" id="episode-details-<?php echo $episode->id?>">
							<div class="row"><span class="label">Start date:</span><?php echo date('jS F, Y',strtotime($episode->start_date))?></div>
							<div class="row"><span class="label">End date:</span><?php echo ($episode->end_date ? date('jS F, Y',strtotime($episode->end_date)) : '-')?></div>
							<div class="row"><span class="label">Principal eye:</span><?php echo $episode->getPrincipalDiagnosis()->getEyeText()?></div>
							<div class="row"><span class="label">Principal diagnosis:</span><?php echo ($episode->getPrincipalDiagnosis() ? $episode->getPrincipalDiagnosis()->disorder->term : 'No diagnosis')?></div>
							<div class="row"><span class="label">Specialty:</span><?php echo CHtml::encode($episode->firm->serviceSpecialtyAssignment->specialty->name)?></div>
							<div class="row"><span class="label">Consultant firm:</span><?php echo CHtml::encode($episode->firm->name)?></span></div>
							<img class="folderIcon"src="/img/_elements/icons/folder_open.png" alt="folder open" />
						</div>
					</div> <!-- .episode -->
				<?php }?>
			</div> <!-- #episodes_sidebar -->
			<div id="event_display">
					<div class="display_actions">
						<div class="display_mode">View mode</div>
						<div class="action_options"><span class="aLabel">you can: </span><span class="aBtn_inactive">View</span><span class="aBtn"><a href="#">Edit</a></span><span class="aBtn"><a href="#">Save</a></span><span class="aBtn"><a href="#">Delete</a></span></div>
					</div>
					<!-- EVENT CONTENT HERE -->
					<div id="event_content">
						<?php
						if (ctype_digit(@$_GET['event'])) {?>
							<div>
							<?php
							$this->renderPartial(
								"/clinical/".$this->getTemplateName('view', $event->event_type_id),
								array(
									'elements' => $elements,
									'eventId' => $_GET['event'],
									'editable' => $editable
								), false, true
							);
						} else {
							$this->renderPartial('/clinical/episodeSummary',
								array('episode' => $current_episode)
							);
						}
						?>
					</div>
					<!-- #event_content -->
					<div class="display_actions footer">
						<div class="action_options"><span class="aLabel">you can: </span><span class="aBtn_inactive">View</span><span class="aBtn"><a href="#">Edit</a></span><span class="aBtn"><a href="#">Save</a></span><span class="aBtn"><a href="#">Delete</a></span></div>
					</div>
			</div><!-- #event_display -->

		</div> <!-- .fullWidth -->
<script type="text/javascript">
	$('a.episode-details').unbind('click').click(function() {
		if ($('#episode-details-'+$(this).attr('rel')).hasClass('hidden')) {
			$('#episode-details-'+$(this).attr('rel')).removeClass('hidden');
		} else {
			$('#episode-details-'+$(this).attr('rel')).addClass('hidden');
		}
		$('div.display_actions').show();
		return false;1
	});

	$('a.show-event-details').unbind('click').click(function() {
		$.ajax({
			url: '/clinical/'+$(this).attr('rel'),
			success: function(data) {
				$('div.display_actions').show();
				$('#event_content').html(data);
			}
		});
		return false;
	});

	$('#addNewEvent').unbind('click').click(function() {
		$.ajax({
			url: '<?php echo Yii::app()->createUrl('clinical/create', array('event_type_id'=>25)); ?>',
			success: function(data) {
				$('div.display_actions').hide();
				$('#event_content').html(data);
			}
		});
		return false;
	});
</script>
