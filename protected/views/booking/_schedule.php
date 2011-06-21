<?php
Yii::app()->clientScript->registerCoreScript('jquery');
Yii::app()->clientScript->registerCSSFile('/css/theatre_calendar.css', 'all');
$patient = $operation->event->episode->patient; ?>
<div id="schedule">
<p><strong>Patient:</strong> <?php echo $patient->first_name . ' ' . $patient->last_name . ' (' . $patient->hos_num . ')'; ?></p>
<p><strong>Operation Details</strong></p>
<div id="operation">
	<h1>Schedule operation > Select theatre slot</h1><br />
<?php
if (Yii::app()->user->hasFlash('info')) { ?>
<div class="flash-error">
    <?php echo Yii::app()->user->getFlash('info'); ?>
</div>
<?php 
} ?>
	<p><strong>Operation duration:</strong> <?php echo $operation->total_duration; ?> minutes</p>
	<?php
$this->widget('zii.widgets.jui.CJuiAccordion', array(
    'panels'=>array(
        'Operation details'=>$this->renderPartial('_operation',
			array('operation' => $operation),true),
    ),
    // additional javascript options for the accordion plugin
    'options'=>array(
		'active'=>false,
        'animated'=>'bounceslide',
		'collapsible'=>true,
    ),
));
$this->widget('zii.widgets.jui.CJuiAccordion', array(
    'panels'=>array(
        'Clinic details'=>$this->renderPartial('_clinic',
			array('operation' => $operation),true),
    ),
    // additional javascript options for the accordion plugin
    'options'=>array(
		'active'=>false,
        'animated'=>'bounceslide',
		'collapsible'=>true,
    ),
));?>
	<strong>Select a session date:</strong><br />
	<div id="calendar">
		<div id="session_dates">
		<div id="details">
<?php	echo $this->renderPartial('_calendar', 
			array('operation'=>$operation, 'date'=>$date, 'sessions' => $sessions), false, true); ?>
		</div>
		<div id="key"><span>KEY:</span>
			<div id="available" class="container"><div class="color_box"></div><div class="label">Slots Available</div></div>
			<div id="limited" class="container"><div class="color_box"></div><div class="label">Limited Slots</div></div>
			<div id="full" class="container"><div class="color_box"></div><div class="label">Full</div></div>
			<div id="closed" class="container"><div class="color_box"></div><div class="label">Theatre Closed</div></div>
			<div id="selected_date" class="container"><div class="color_box"></div><div class="label">Selected Date</div></div>
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
				'url': 'index.php?r=booking/sessions',
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
				'url': 'index.php?r=booking/sessions',
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
				'url': 'index.php?r=booking/theatres',
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
					$( "#theatres" ).tabs();
				}
			});
		});
		$('#theatres button').live('click', function() {
			var session = $(this).children('.session_id').text();
			var month = $('#current_month').text();
			var operation = $('input[id=operation]').val();
			var day = $('.selected_date').text();
			$.ajax({
				'url': 'index.php?r=booking/list',
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
		$('#confirm').live('click', function() {
			var operation = $('input[id=operation]').val();
			var session = $('input[name=session_id]').val();
			console.log('operation');
			console.log(operation);
			console.log('session');
			console.log(session);
			$.ajax({
				'url': 'index.php?r=booking/create',
				'type': 'POST',
				'data': {
					'Booking': {
						'element_operation_id': operation,
						'session_id': session
					}
				},
				'success': function(data) {
					$('#bookings').append(data);
				}
			});
		});
	});
</script>