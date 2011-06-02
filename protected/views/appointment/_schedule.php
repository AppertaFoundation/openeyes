<?php
Yii::app()->clientScript->registerCoreScript('jquery');
Yii::app()->clientScript->registerCSSFile('/css/theatre_calendar.css', 'all');
$patient = $operation->event->episode->patient; ?>
<div id="schedule">
<p>Patient: <?php echo $patient->first_name . ' ' . $patient->last_name . ' (' . $patient->hos_num . ')'; ?></p>
<p>Operation Details</p>
<div id="operation">
	<h1>Schedule operation > Select theatre slot</h1><br />
<?php
if (Yii::app()->user->hasFlash('info')) { ?>
<div class="flash-error">
    <?php echo Yii::app()->user->getFlash('info'); ?>
</div>
<?php 
} ?>
	<strong>Currently selected date:</strong> Date Here<br />
	<strong>Operation duration:</strong> <?php echo $operation->total_duration; ?> minutes<br />
	<strong>Select a session date:</strong><br />
	<div id="calendar">
		<div id="details">
<?php	echo $this->renderPartial('_calendar', 
			array('operation'=>$operation, 'date'=>$date, 'sessions' => $sessions), false, true); ?>
		</div>
		<div id="key">
			KEY: <span class="available">Slots Available</span>
			<span class="limited">Limited Slots</span>
			<span class="full">Full</span>
			<span class="closed">Theatre Closed</span>
			<span class="selected_date">Selected Date</span>
		</div>
	</div>
</div>
</div>
<script type="text/javascript">
	$(function() {
		$('#previous_month').click(function() {
			var month = $('input[id=pmonth]').val();
			var operation = $('input[id=operation]').val();
			console.log(month);
			console.log(operation);
			$.ajax({
				'url': 'index.php?r=appointment/sessions',
				'type': 'GET',
				'data': {'operation': operation, 'date': month},
				'success': function(data) {
					$('#details').html(data);
				}
			});
			return false;
		});
		$('#next_month').click(function() {
			var month = $('input[id=nmonth]').val();
			var operation = $('input[id=operation]').val();
			console.log(month);
			console.log(operation);
			$.ajax({
				'url': 'index.php?r=appointment/sessions',
				'type': 'GET',
				'data': {'operation': operation, 'date': month},
				'success': function(data) {
					$('#details').html(data);
				}
			});
			return false;
		});
	});
</script>