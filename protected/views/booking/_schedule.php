<?php
Yii::app()->clientScript->registerCSSFile('/css/theatre_calendar.css', 'all');
$patient = $operation->event->episode->patient; ?>
<div id="schedule">
<p><strong>Patient:</strong> <?php echo $patient->first_name . ' ' . $patient->last_name . ' (' . $patient->hos_num . ')'; ?></p>
<h3>Schedule Operation</h3>
<div id="operation">
	<h1>Select theatre slot</h1><br />
<?php
if (Yii::app()->user->hasFlash('info')) { ?>
<div class="flash-notice">
    <?php echo Yii::app()->user->getFlash('info'); ?>
</div>
<?php 
} ?>
	<strong>Select a session date:</strong><br />
	<div id="calendar">
		<div id="session_dates">
		<div id="details">
<?php	echo $this->renderPartial('_calendar', 
			array('operation'=>$operation, 'date'=>$date, 'sessions' => $sessions), false, true); ?>
		</div>
		</div>
	</div>
</div>
</div>
<script type="text/javascript">
	$(function() {
		$('#previous_month').live('click',function() {
			var month = $('input[id=pmonth]').val();
			var operation = $('input[id=operation]').val();
			$.ajax({
				'url': '<?php echo Yii::app()->createUrl('booking/sessions'); ?>',
				'type': 'GET',
				'data': {'operation': operation, 'date': month},
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
		$('#next_month').live('click',function() {
			var month = $('input[id=nmonth]').val();
			var operation = $('input[id=operation]').val();
			$.ajax({
				'url': '<?php echo Yii::app()->createUrl('booking/sessions'); ?>',
				'type': 'GET',
				'data': {'operation': operation, 'date': month},
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
		$('#calendar table td.available,#calendar table td.limited,#calendar table td.full').live('click', function() {
			$('.selected_date').removeClass('selected_date');
			$(this).addClass('selected_date');
			var day = $(this).text();
			var month = $('#current_month').text();
			var operation = $('input[id=operation]').val();
			$.ajax({
				'url': '<?php echo Yii::app()->createUrl('booking/theatres'); ?>',
				'type': 'GET',
				'data': {'operation': operation, 'month': month, 'day': day},
				'success': function(data) {
					if ($('#theatres').length == 0) {
						$('#operation').append(data);
					} else {
						$('#theatres').replaceWith(data);
					}
					if ($('#bookings').length > 0) {
						$('#bookings').remove();
					}
					//$("#theatres").tabs();
				}
			});
		});
		$('#theatres div.shinybutton').live('click', function() {
			var session = $(this).children().children('span.session_id').text();
			var month = $('#current_month').text();
			var operation = $('input[id=operation]').val();
			var day = $('.selected_date').text();
			$(this).siblings().removeClass('highlighted');
			$(this).addClass('highlighted');
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
		});
	});
</script>