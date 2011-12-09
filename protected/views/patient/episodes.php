		<h2>Episodes &amp; Events</h2>
		<script type="text/javascript"> var header_text = ''; </script>
		<div class="fullWidth fullBox clearfix">
			<div id="episodesBanner whiteBox">
				<form><button style="float: right; margin-right: 1px;" type="submit" id="addNewEvent" class="classy green" tabindex="2"><span class="button-icon-green">+</span><span class="button-span button-span-green">&nbsp;&nbsp;add new Event</span></button></form>
				<p><strong>&nbsp;<?php if (count($episodes) <1) {?>No Episodes for this patient<?php }?></strong></p>
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
					<div class="episode <?php echo empty($episode->end_date) ? 'closed' : 'open' ?> clearfix">
						<div class="episode_nav">
							<input type="hidden" name="episode-id" value="<?php echo $episode->id?>" />
							<div class="small"><?php echo date('d M Y',strtotime($episode->start_date))?><span style="float:right;"><a href="#" rel="<?php echo $episode->id?>" class="episode-details">(Episode) summary</a><span></div>
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

									if (ctype_digit(@$_GET['event']) && $_GET['event'] == $event->id) {
										$highlight = true;
									} else {
										$highlight = false;
									}
									?>
									<li id="eventLi<?php echo $event->id ?>"><a href="#" rel="<?php echo $event->id?>" class="show-event-details"><?php if ($highlight) echo '<div class="viewing">'?><span class="type"><img src="/img/_elements/icons/event_op_<?php if (!$scheduled) echo 'un'?>scheduled.png" alt="op" width="16" height="16" /></span><span class="date"> <?php echo date('d M Y',strtotime($event->datetime))?></span><?php if ($highlight) echo '</div>' ?></a></li>
							<?php
								}
							?>
							</ul>
						</div>
						<div class="episode_details hidden" id="episode-details-<?php echo $episode->id?>">
							<div class="row"><span class="label">Start date:</span><?php echo date('d M Y',strtotime($episode->start_date))?></div>
							<div class="row"><span class="label">End date:</span><?php echo ($episode->end_date ? date('d M Y',strtotime($episode->end_date)) : '-')?></div>
							<?php $diagnosis = $episode->getPrincipalDiagnosis() ?>
							<div class="row"><span class="label">Principal eye:</span><?php echo !empty($diagnosis) ? $diagnosis->getEyeText() : 'No diagnosis' ?></div>
							<div class="row"><span class="label">Principal diagnosis:</span><?php echo !empty($diagnosis) ? $diagnosis->disorder->term : 'No diagnosis' ?></div>
							<div class="row"><span class="label">Specialty:</span><?php echo CHtml::encode($episode->firm->serviceSpecialtyAssignment->specialty->name)?></div>
							<div class="row"><span class="label">Consultant firm:</span><?php echo CHtml::encode($episode->firm->name)?></span></div>
							<img class="folderIcon"src="/img/_elements/icons/folder_open.png" alt="folder open" />
						</div>
					</div> <!-- .episode -->
				<?php }?>
			</div> <!-- #episodes_sidebar -->
			<div id="event_display">
				<div id="add-event-select-type" class="whiteBox addEvent clearfix" style="display: none;">
					<h3>Adding New Event</h3>
					<p><strong>Select event to add:</strong></p>
					<?php
						foreach ($eventTypes as $eventType) {
?>
					<p><a href="#" id="add-new-event-type<?php echo $eventType->id ?>"><img src="/img/_elements/icons/event_op_unscheduled.png" alt="operation" width="16" height="16" /> - <strong><?php echo $eventType->name ?></strong></a></p>
<?php
						}
?>
				</div>
				<input type="hidden" id="edit-eventid" name="edit-eventid" value="<?php if (ctype_digit(@$_GET['event'])) echo $_GET['event']?>" />
				<?php
				if (!isset($current_episode)) {?>
					<div class="alertBox fullWidthEvent">
						<h4>There are currently no episodes for this patient, please add a new event to open an episode.</h4>
					</div>
				<?php }?>
				<div class="display_actions"<?php if (!isset($current_episode)) {?> style="display: none;"<?php }?>>
					<div class="display_mode">View mode</div>
					<div class="action_options"<?php if (!ctype_digit(@$_GET['event'])){?> style="display: none;"<?php }?>>
						<span class="aBtn_inactive">View</span><span class="aBtn edit-event"<?php if (!$editable){?> style="display: none;"<?php }?>><a class="edit-event" href="#">Edit</a></span>
					</div>
					<div class="action_options_alt" style="display: none;">
						<span class="aBtn save"><a href="#" class="edit-save">Save</a></span><span class="aBtn cancel"><a href="#" class="edit-cancel">Cancel</a></span>
					</div>
				</div>
				<div class="colorband category_treatement"></div>
				<!-- EVENT CONTENT HERE -->
				<div id="event_content" class="watermarkBox fullWidthEvent" style="background:#fafafa url(/img/_elements/icons/event/watermark/_blank.png) top left repeat-y;">
					<?php
					if (ctype_digit(@$_GET['event'])) {?>
						<?php
						$this->renderPartial(
							"/clinical/".$this->getTemplateName('view', $event->event_type_id),
							array(
								'elements' => $elements,
								'eventId' => $_GET['event'],
								'editable' => $editable,
								'site' => $site
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
				<!-- #event_content -->
				<div class="colorband category_treatement"></div>
				<div id="display_actions_footer" class="display_actions footer"<?php if (!isset($current_episode)) {?> style="display: none;"<?php }?>>
					<div class="action_options"<?php if (!ctype_digit(@$_GET['event'])){?> style="display: none;"<?php }?>>
						<span class="aBtn_inactive">View</span><span class="aBtn edit-event"<?php if (!$editable){?> style="display: none;"<?php }?>><a class="edit-event" href="#">Edit</a></span>
					</div>
					<div class="action_options_alt" style="display: none;">
						<span class="aBtn save"><a href="#" class="edit-save">Save</a></span><span class="aBtn cancel"><a href="#" class="edit-cancel">Cancel</a></span>
					</div>
				</div>
			</div><!-- #event_display -->
		</div> <!-- .fullWidth -->
		<script type="text/javascript">
		<?php
			if (ctype_digit(@$_GET['event'])) {
		?>
			var currentEvent = <?php echo $_GET['event'] ?>;
			var header_text = '';
		<?php
			} else {
		?>
			var currentEvent = '';
		<?php
			}
		?>

		<?php if (ctype_digit(@$_GET['event'])) {?>
			var last_item_type = 'event';
			var last_item_id = <?php echo $_GET['event']?>;
		<?php }else if (isset($current_episode)) {?>
			var last_item_type = 'episode';
			var last_item_id = <?php echo $current_episode->id?>;
		<?php }else{?>
			var last_item_type = 'url';
			var last_item_id = window.location.href;
		<?php }?>

			$('a.episode-details').unbind('click').click(function() {
				load_episode_summary($(this).attr('rel'));
				return false;
			});

			function load_episode_summary(id) {
				$.ajax({
					url: '/clinical/episodesummary/'+id,
					success: function(data) {
						last_item_type = 'episode';
						last_item_id = id;

						$('div.action_options').hide();
						$('#event_content').html(data);
						view_mode();

						if (currentEvent != '') {
							// An event was highlighted previously so recreate the a it had
							$('li[id=eventLi' + currentEvent + ']').wrapInner('<a href="#" rel="' + currentEvent + '" class="show-event-details" />');
							currentEvent = '';
							var content = $(".viewing").contents()
							$(".viewing").replaceWith(content);
						}

					}
				});
				return false;
			}

			$('a.show-event-details').die('click').live('click', function() {
				var event_id = $(this).attr('rel');
				view_event(event_id);
				// Highlight event clicked - get child of element. If it's a div do nothing. If it's a span blank all other elements of this class and add a div to this span

				// Get rid of all "viewing" divs
				var content = $(".viewing").contents()
				$(".viewing").replaceWith(content);

				// Wrap contents of chosen a in the "viewing" div
				$(this).wrapInner('<div class="viewing" />');

				// Get rid of the a
				var content = $(this).contents()
				$(this).replaceWith(content);

				if (currentEvent != '') {
					// An event was highlighted previously so recreate the a it had
					$('li[id=eventLi' + currentEvent + ']').wrapInner('<a href="#" rel="' + currentEvent + '" class="show-event-details" />');
				}

				// Prepare for the next click
				currentEvent = event_id;

				return false;
			});

			function view_event(event_id) {
				$.ajax({
					url: '/clinical/'+event_id,
					success: function(data) {
						$('#edit-eventid').val(event_id);
						$('div.display_actions').show();
						$('#display_actions_footer').show();
						$('div.action_options').show();
						$('div.action_options_alt').hide();
						$('#event_content').html(data);

						$('.display_mode').html(header_text);

						if (data.match(/<!-- editable -->/)) {
							$('span.edit-event').show();
						} else {
							$('span.edit-event').hide();
						}

						last_item_type = 'event';
						last_item_id = event_id;

						view_mode();
					}
				});
			}

			$(document).ready(function(){
				if (header_text) $('.display_mode').html(header_text);

				$collapsed = true;

				$('#addNewEvent').unbind('click').click(function(e) {
					e.preventDefault();
					$collapsed = false;

					$('#add-event-select-type').slideToggle(100,function() {
						if($(this).is(":visible")){
							$('#addNewEvent').removeClass('green').addClass('inactive');
							$('#addNewEvent span.button-span-green').removeClass('button-span-green').addClass('button-span-inactive');
							$('#addNewEvent span.button-icon-green').removeClass('button-icon-green').addClass('button-icon-inactive');
						} else {
							$('#addNewEvent').removeClass('inactive').addClass('green');
							$('#addNewEvent span.button-span-inactive').removeClass('button-span-inactive').addClass('button-span-green');
							$('#addNewEvent span.button-icon-inactive').removeClass('button-icon-inactive').addClass('button-icon-green');
							$collapsed = true;
						}
						return false;
					});

					return false;
				});
			});

			$('a[id^="add-new-event-type"]').unbind('click').click(function() {
				eventTypeId = this.id.match(/\d*$/);
				$.ajax({
					url: '/clinical/create?event_type_id=' + eventTypeId,
					success: function(data) {
						$('.display_mode').removeClass('edit').addClass('add');
						//$('div.display_actions').hide();
						$('#add-event-select-type').hide();
						$collapsed = true;
						$('#addNewEvent').removeClass('inactive').addClass('green');
						$('#addNewEvent span.button-span-inactive').removeClass('button-span-inactive').addClass('button-span-green');
						$('#addNewEvent span.button-icon-inactive').removeClass('button-icon-inactive').addClass('button-icon-green');
						$('#event_content').html(data);
						$('.display_mode').html(header_text);
					}
				});
				return false;
			});

			$('a.edit-event').unbind('click').click(function() {
				edit_event($('#edit-eventid').val());
				return false;
			});

			function edited() {
				$('div.action_options').hide();
				$('div.action_options_alt').show();
			}

			function edit_event(event_id) {
				$.ajax({
					url: '/clinical/update/'+event_id,
					success: function(data) {
						edit_mode();
						$('div.display_actions').show();
						$('#event_content').html(data);
					}
				});
			}

			function edit_mode() {
				$('.display_mode').removeClass('add').addClass('edit');

				$('div.action_options').html('<span class="aBtn"><a class="view-event" href="#">View</a></span><span class="aBtn_inactive edit-event">Edit</span>');
				$('a.view-event').unbind('click').click(function() {
					view_event($('#edit-eventid').val());
					view_mode();
					return false;
				});
			}

			function view_mode() {
				$('.display_mode').removeClass('edit').removeClass('add');;

				$('div.action_options').html('<span class="aBtn_inactive">View</span><span class="aBtn edit-event"><a class="edit-event" href="#">Edit</a></span>');
				$('a.edit-event').unbind('click').click(function() {
					edit_event($('#edit-eventid').val());
					edit_mode();
					return false;
				});
			}
		</script>
