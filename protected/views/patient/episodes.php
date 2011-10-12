		<h2>Episodes &amp; Events</h2>
		<div class="fullWidth fullBox clearfix">
			<div id="episodesBanner whiteBox">
				<form><button type="submit" value="submit" class="btn_newEvent ir" id="addNewEvent">Add New Event</button></form>
				<p>&nbsp;</p>
			</div>
			<div id="episodes_sidebar">
				<?php foreach ($episodes as $i => $episode) {
					if (isset($_GET['episode']) && ctype_digit($_GET['episode'])) {
						if ($episode->id == $_GET['episode']) {
							$current_episode = $episode;
						}
					} else {
						if ($i == 0) $current_episode = $episode;
					}
					?>
					<div class="episode open clearfix">
						<div class="episode_nav">
							<input type="hidden" name="episode-id" value="<?php echo $episode->id?>" />
							<div class="small"><?php echo date('d.m.Y',strtotime($episode->start_date))?><span style="float:right;"><a href="#" rel="<?php echo $episode->id?>" class="episode-details">Details</a><span></div>
							<h4><?php echo CHtml::encode($episode->firm->serviceSpecialtyAssignment->specialty->name)?></h4>
							<ul class="events">
								<?php foreach ($episode->events as $event) {?>
									<?php
									$event_elements = $this->service->getElements(
										null, null, null, 0, $event
									);
									$scheduled = false;
									foreach ($event_elements as $element) {
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
				<div id="add-event-select-type" class="whiteBox addEvent clearfix hidden">
					<h3>Adding New Event</h3>
					<p><strong>Select event to add:</strong></p>
					<p><a href="#" id="add-operation"><img src="/img/_elements/icons/event_op_unscheduled.png" alt="operation" width="16" height="16" /> - <strong>Operation</strong></a></p>
				</div>
				<input type="hidden" id="edit-eventid" name="edit-eventid" value="<?php if (ctype_digit(@$_GET['event'])) echo $_GET['event']?>" />
				<?php
				if (!isset($current_episode)) {?>
					<h4>There are currently no episodes for this patient.</h4>
				<?php }?>
				<div class="display_actions"<?php if (!isset($current_episode)) {?> style="display: none;"<?php }?>>
					<div class="display_mode">View mode</div>
					<div class="action_options" style="display: none;"><span class="aBtn_inactive">View</span><span class="aBtn"><a class="edit-event" href="#">Edit</a></span><?php /*<span class="aBtn"><a href="#">Save</a></span><span class="aBtn"><a href="#">Delete</a></span>*/?></div>
				</div>
				<!-- EVENT CONTENT HERE -->
				<div id="event_content" class="eventBox fullWidthEvent">
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
						if (isset($current_episode)) {
							$this->renderPartial('/clinical/episodeSummary',
								array('episode' => $current_episode)
							);
						}
					}
					?>
				</div>
				</div>
				<!-- #event_content -->
				<div id="display_actions_footer" class="display_actions footer"<?php if (!isset($current_episode)) {?> style="display: none;"<?php }?>>
					<div class="action_options" style="display: none;"><span class="aBtn_inactive">View</span><span class="aBtn"><a class="edit-event" href="#">Edit</a></span><?php /*<span class="aBtn"><a href="#">Save</a></span><span class="aBtn"><a href="#">Delete</a></span>*/?></div>
				</div>
			</div><!-- #event_display -->
		</div> <!-- .fullWidth -->
		<script type="text/javascript">
			$('a.episode-details').unbind('click').click(function() {
				$.ajax({
					url: '/clinical/episodesummary/'+$(this).attr('rel'),
					success: function(data) {
						$('div.action_options').hide();
						$('#event_content').html(data);
					}
				});
				return false;
			});

			$('a.show-event-details').unbind('click').click(function() {
				var event_id = $(this).attr('rel');

				$.ajax({
					url: '/clinical/'+$(this).attr('rel'),
					success: function(data) {
						$('#edit-eventid').val(event_id);
						$('div.display_actions').show();
						$('#display_actions_footer').show();
						$('div.action_options').show();
						$('#event_content').html(data);
					}
				});
				return false;
			});

			$('#addNewEvent').unbind('click').click(function() {
				if ($('#add-event-select-type').hasClass('hidden')) {
					$('#add-event-select-type').removeClass('hidden');
				} else {
					$('#add-event-select-type').addClass('hidden');
				}
				return false;
			});

			$('#add-operation').unbind('click').click(function() {
				$.ajax({
					url: '<?php echo Yii::app()->createUrl('clinical/create', array('event_type_id'=>25)); ?>',
					success: function(data) {
						$('div.display_actions').hide();
						$('#add-event-select-type').addClass('hidden');
						$('#event_content').html(data);
					}
				});
				return false;
			});

			$('a.edit-event').unbind('click').click(function() {
				$.ajax({
					url: '/clinical/update/'+$('#edit-eventid').val(),
					success: function(data) {
						$('div.display_actions').show();
						$('div.action_options').show();
						$('#event_content').html(data);
					}
				});
			});
		</script>
