<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
		<h2>Episodes &amp; Events</h2>
		<div class="fullWidth fullBox clearfix">
			<div id="episodesBanner">
				<form>
					<button tabindex="2" class="classy green" id="addNewEvent" type="submit" style="float: right; margin-right: 1px;"><span class="button-span button-span-green with-plussign">add new Event</span></button>
				</form>
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
							<div class="small"><?php echo $episode->NHSDate('start_date'); ?><span style="float:right;"><a href="#" rel="<?php echo $episode->id?>" class="episode-details">(Episode) summary</a></span></div>
							<h4><?php echo CHtml::encode($episode->firm->serviceSubspecialtyAssignment->subspecialty->name)?></h4>
							<ul class="events">
								<?php foreach ($episode->events as $event) {?>
									<?php
									$event_elements = $this->service->getDefaultElements($event);
									$scheduled = false;
									foreach ($event_elements as $element) {
										if (get_class($element) == 'ElementOperation' && in_array($element->status, array(ElementOperation::STATUS_SCHEDULED, ElementOperation::STATUS_RESCHEDULED))) {
											$scheduled = true;
										}
									}

									if (ctype_digit(@$_GET['event']) && $_GET['event'] == $event->id) {
										$highlight = true;
									} else {
										$highlight = false;
									}
									?>
									<li id="eventLi<?php echo $event->id ?>"><a href="#" rel="<?php echo $event->id?>" class="show-event-details"><?php if ($highlight) echo '<div class="viewing">'?><span class="type"><img src="/img/_elements/icons/event/small/treatment_operation<?php if (!$scheduled) { echo '_unscheduled'; } else { echo '_booked';}?>.png" alt="op" width="16" height="16" /></span><span class="date"> <?php echo $event->NHSDateAsHTML('datetime'); ?></span><?php if ($highlight) echo '</div>' ?></a></li>
							<?php
								}
							?>
							</ul>
						</div>
						<div class="episode_details hidden" id="episode-details-<?php echo $episode->id?>">
							<div class="row"><span class="label">Start date:</span><?php echo $episode->NHSDate('start_date'); ?></div>
							<div class="row"><span class="label">End date:</span><?php echo ($episode->end_date ? $episode->NHSDate('end_date') : '-')?></div>
							<?php $diagnosis = $episode->getPrincipalDiagnosis() ?>
							<div class="row"><span class="label">Principal eye:</span><?php echo !empty($diagnosis) ? $diagnosis->getEyeText() : 'No diagnosis' ?></div>
							<div class="row"><span class="label">Principal diagnosis:</span><?php echo !empty($diagnosis) ? $diagnosis->disorder->term : 'No diagnosis' ?></div>
							<div class="row"><span class="label">Subspecialty:</span><?php echo CHtml::encode($episode->firm->serviceSubspecialtyAssignment->subspecialty->name)?></div>
							<div class="row"><span class="label">Consultant firm:</span><?php echo CHtml::encode($episode->firm->name)?></div>
							<img class="folderIcon" src="/img/_elements/icons/folder_open.png" alt="folder open" />
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
					<p><a href="#" id="add-new-event-type<?php echo $eventType->id ?>"><img src="/img/_elements/icons/event/small/treatment_operation_unscheduled.png" alt="operation" width="16" height="16" /> - <strong><?php echo $eventType->name ?></strong></a></p>
<?php
						}
?>
				</div>
				<input type="hidden" id="edit-eventid" name="edit-eventid" value="<?php if (ctype_digit(@$_GET['event'])) echo $_GET['event']?>" />
				<?php
				if (!isset($current_episode)) {?>
					<div class="alertBox fullWidthEvent">
						There are currently no episodes for this patient, please add a new event to open an episode.
					</div>
				<?php }?>
				<div class="display_actions"<?php if (!isset($current_episode)) {?> style="display: none;"<?php }?>>
					<div class="display_mode">Episode summary</div>
					<div class="action_options"<?php if (!ctype_digit(@$_GET['event'])){?> style="display: none;"<?php }?>>
						<span class="aBtn_inactive">View</span><span class="aBtn edit-event"<?php if (!$editable){?> style="display: none;"<?php }?>><a class="edit-event" href="#">Edit</a></span>
					</div>
					<div class="action_options_alt" style="display: none;">
						<span class="aBtn save"><a href="#" class="edit-save">Save</a></span><span class="aBtn cancel"><a href="#" class="edit-cancel">Cancel</a></span>
					</div>
				</div>
				<div class="colorband category_treatement"></div>
				<!-- EVENT CONTENT HERE -->
				<div id="event_content" class="watermarkBox fullWidthEvent" style="background:#fafafa;">
					<?php
					if (ctype_digit(@$_GET['event'])) {?>
						<?php
						if ($event->eventType->class_name == 'OphTrOperation') {
							$this->renderPartial(
								"/clinical/".$this->getTemplateName('view', $event->event_type_id),
								array( 'elements' => $elements, 'eventId' => $_GET['event'], 'editable' => $editable, 'site' => $site), 
								false, true
							);
						} else {
							echo "fish - not getting here, nor should we"; exit;
							$this->renderPartial(
								Yii::app()->basePath . DIRECTORY_SEPARATOR . $event->eventType->class_name . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "default" . DIRECTORY_SEPARATOR . "index",
								array( 'elements' => $elements, 'eventId' => $_GET['event'], 'editable' => $editable, 'site' => $site), 
								false, true
							);
						}
					} else {
						if (isset($current_episode)) {
							$this->renderPartial('/clinical/episodeSummary',
								array('episode' => $current_episode, 'patient' => $model)
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

						$('.display_mode').html('Episode summary');

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

						if ($('#header_text').length >0) {
							$('.display_mode').html($('#header_text').html());
						}

						view_mode();

						if (data.match(/<!-- editable -->/)) {
							$('span.edit-event').show();
						} else {
							$('span.edit-event').hide();
						}

						last_item_type = 'event';
						last_item_id = event_id;
					}
				});
			}

			var eventTypeClasses = new Array();
			<?php foreach ($eventTypes as $eventType) { ?>
				eventTypeClasses[<?php echo $eventType->id ?>] = '<?php echo $eventType->class_name ?>';
			<?php } ?>

			$(document).ready(function(){
				if ($('#header_text').length >0) {
					$('.display_mode').html($('#header_text').html());
				}

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
				var firm_id = $('#selected_firm_id option:selected').val();

				var eventTypeName = eventTypeClasses[eventTypeId];
				var module_address = '';
				if (eventTypeName == 'OphTrOperation') {
					module_address = 'clinical';	
				} else {
					module_address = eventTypeName + '/Default';
				}
				$.ajax({
					url: '/' + module_address + '/create?event_type_id=' + eventTypeId + '&patient_id=<?php echo $model->id?>&firm_id='+firm_id,
					success: function(data) {
						$('.display_mode').removeClass('edit').addClass('add');
						//$('div.display_actions').hide();
						$('#add-event-select-type').hide();
						$collapsed = true;
						$('#addNewEvent').removeClass('inactive').addClass('green');
						$('#addNewEvent span.button-span-inactive').removeClass('button-span-inactive').addClass('button-span-green');
						$('#addNewEvent span.button-icon-inactive').removeClass('button-icon-inactive').addClass('button-icon-green');
						$('#event_content').html(data);

						if ($('#header_text').length >0) {
							$('.display_mode').html($('#header_text').html());
						}
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
				var eventTypeName = eventTypeClasses[eventTypeId];
				var module_address = '';
				if (eventTypeName == 'OphTrOperation') {
					module_address = 'clinical';	
				} else {
					module_address = eventTypeName + '/Default';
				}
				$.ajax({
					url: '/' + module_address + '/update/'+event_id,
					success: function(data) {
						edit_mode();
						$('div.display_actions').show();
						$('#event_content').html(data);
					},
					error: function(req,error) {
						alert("Sorry, you do not have rights to edit this event. You may have selected a different firm in another browser tab.");
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

				$('div.action_options_alt').hide();
			}
		</script>
