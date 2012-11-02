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

$this->header();

$patient = $operation->event->episode->patient;

?>
<div id="schedule">
	<div class="patientReminder">
		<span class="patient"><?php echo $patient->getDisplayName()?> (<?php echo $patient->hos_num ?>)</span>
	</div>

	<h3>Schedule Operation</h3>

	<?php
	if ($operation->event->episode->firm_id != $firm->id) {
		if ($firm->name == 'Emergency List') {
			$class = 'flash-error';
			$message = 'You are booking into the Emergency List.';
		} else {
			$class = 'flash-notice';
			$message = 'You are booking into the list for ' . $firm->name . '.';
		} ?>
		<div class="<?php echo $class; ?>"><?php echo $message; ?></div>
		<?php
	}
	if (empty($sessions)) { ?>
		<div class="flash-error">This firm has no scheduled sessions.</div>
		<?php
	}
	?>

	<div id="firmSelect" class="eventDetail clearfix">
		<div class="label"><span class="normal">Viewing the schedule for </span><br /><strong><?php echo $firm->name?></strong></div>
		<div class="data">
			<select id="firmId">
				<option value="">Select a different firm</option>
				<option value="EMG">Emergency List</option>
				<?php foreach ($firmList as $id => $name) {?>
					<option value="<?php echo $id ?>"><?php echo $name ?></option>
				<?php }?>
			</select>
		</div>
	</div>

<div id="operation">
	<h3>Select theatre slot</h3>

	<?php if (Yii::app()->user->hasFlash('info')) {?>
		<div class="flash-notice">
			<?php echo Yii::app()->user->getFlash('info'); ?>
		</div>
	<?php }?>

	<h4>Select a session date:</h4>
	<div id="calendar">
		<div id="session_dates">
			<div id="details">
				<?php echo $this->renderPartial('_calendar', array('operation'=>$operation, 'date'=>$date, 'sessions' => $sessions, 'firmId' => $firm->id), false, true); ?>
			</div> <!-- details -->
		</div> <!--session_dates -->
	</div> <!-- calendar -->
</div> <!-- operation -->
</div> <!-- #schedule -->

<script type="text/javascript">
	$(function() {
		$(this).undelegate('#previous_month','click').delegate('#previous_month','click',function() {
			var month = $('input[id=pmonth]').val();
			var operation = $('input[id=operation]').val();
			$.ajax({
				'url': '<?php echo Yii::app()->createUrl('booking/sessions'); ?>',
				'type': 'GET',
				'data': {'operation': operation, 'date': month, 'firmId': '<?php echo empty($firm->id) ? 'EMG' : $firm->id ?>'},
				'success': function(data) {
					$('#details').html(data);
					if ($('#theatres').length > 0) {
						$('#theatres').remove();
					}
					if ($('#bookings').length > 0) {
						$('#bookings').remove();
					}
				}
			});
			return false;
		});
		$(this).undelegate('#next_month','click').delegate('#next_month','click',function() {
			var month = $('input[id=nmonth]').val();
			var operation = $('input[id=operation]').val();
			$.ajax({
				'url': '<?php echo Yii::app()->createUrl('booking/sessions'); ?>',
				'type': 'GET',
				'data': {'operation': operation, 'date': month, 'firmId': '<?php echo empty($firm->id) ? 'EMG' : $firm->id ?>'},
				'success': function(data) {
					$('#details').html(data);
					if ($('#theatres').length > 0) {
						$('#theatres').remove();
					}
					if ($('#bookings').length > 0) {
						$('#bookings').remove();
					}
				}
			});
			return false;
		});
		$(this).undelegate('#calendar table td.available,#calendar table td.limited,#calendar table td.full,#calendar table td.inthepast,#calendar table td.closed','click').delegate('#calendar table td.available,#calendar table td.limited,#calendar table td.full,#calendar table td.inthepast,#calendar table td.closed','click',function() {
			$('#sessionDetails').html('');
			$('.selected_date').removeClass('selected_date');
			$(this).addClass('selected_date');
			var day = $(this).text();
			var month = $('#current_month').text();
			var operation = $('input[id=operation]').val();
			$.ajax({
				'url': '<?php echo Yii::app()->createUrl('booking/theatres'); ?>',
				'type': 'GET',
				'data': {'operation': operation, 'month': month, 'day': day, 'firm': '<?php echo empty($firm->id) ? 'EMG' : $firm->id ?>', 'reschedule': 0},
				'success': function(data) {
					if ($('#theatres').length == 0) {
						$('#operation').append(data);
					} else {
						$('#theatres').replaceWith(data);
					}
					if ($('#bookings').length > 0) {
						$('#bookings').remove();
					}
					if ($('#theatres div.shinybutton').length == 1) {
						var button = $('#theatres div.shinybutton');
						var session = button.children().children('span.session_id').text();
						button.addClass('highlighted');
						showTheatreList(operation, month, day, session);
					}
				}
			});
		});
		$(this).undelegate('#theatres div.shinybutton','click').delegate('#theatres div.shinybutton','click',function() {
			var session = $(this).children().children('span.session_id').text();
			var month = $('#current_month').text();
			var operation = $('input[id=operation]').val();
			var day = $('.selected_date').text();
			$(this).siblings().removeClass('highlighted');
			$(this).addClass('highlighted');
			showTheatreList(operation, month, day, session);
		});
		$(this).undelegate('#firmSelect #firmId','change').delegate('#firmSelect #firmId','change',function() {
			var firmId = $(this).val();
			var operation = $('input[id=operation]').val();
			window.location.href = '<?php echo Yii::app()->createUrl('booking/schedule'); ?>?operation='+operation+'&firmId='+firmId;
		});
	});

	function showTheatreList(operation, month, day, session) {
		$.ajax({
			'url': '<?php echo Yii::app()->createUrl('booking/list'); ?>',
			'type': 'GET',
			'data': {
				'operation': operation,
				'month': month,
				'day': day,
				'session': session,
			},
			'success': function(data) {
				if ($('#bookings').length == 0) {
					$('#operation').append(data);
				} else {
					$('#bookings').replaceWith(data);
				}
			}
		});
	}
</script>
<?php $this->footer()?>
