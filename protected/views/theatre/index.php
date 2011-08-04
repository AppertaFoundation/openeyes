<?php
Yii::app()->clientScript->registerCSSFile('/css/theatre.css', 'all'); ?>
<div id="box_gradient_top"></div>
<div id="box_gradient_bottom">
<h3>Theatre Schedule</h3>
<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'theatre-filter',
	'action'=>Yii::app()->createUrl('theatre'),
    'enableAjaxValidation'=>false,
)); ?>
<strong>Show schedule by:</strong><br />
<div id="search-options">
	<strong>Site:</strong>
<?php
	echo CHtml::dropDownList('site-id', $siteId, Site::model()->getList(), 
		array('empty'=>'All sites', 'ajax'=>array(
			'type'=>'POST',
			'data'=>array('site_id'=>'js:this.value'),
			'url'=>Yii::app()->createUrl('theatre/filterTheatres'),
			'success'=>"js:function(data) {
				$('#theatre-id').html(data);
			}",
		))); ?>
	<strong>Service:</strong>
<?php
	echo CHtml::dropDownList('service-id', $serviceId, Service::model()->getList(), 
		array('empty'=>'All services', 'ajax'=>array(
			'type'=>'POST',
			'data'=>array('service_id'=>'js:this.value'),
			'url'=>Yii::app()->createUrl('theatre/filterFirms'),
			'success'=>"js:function(data) {
				$('#firm-id').attr('disabled', false);
				$('#firm-id').html(data);
			}",
		))); ?>
	<strong>Firm:</strong>
<?php
	echo CHtml::dropDownList('firm-id', $firmId, $firmList, 
		array('empty'=>'All firms', 'disabled'=>(empty($firmId)))); ?>
	<strong>Theatre:</strong>
<?php
	echo CHtml::dropDownList('theatre-id', $theatreId, $theatreList, 
		array('empty'=>'All theatres')); ?>
</div>
<div>
<?php
	echo CHtml::radioButtonList('date-filter', $dateFilter, Theatre::getDateFilterOptions(), 
		array('separator' => '&nbsp;')); ?>
<?php
$this->widget('zii.widgets.jui.CJuiDatePicker', array(
    'name'=>'date-start',
	'id'=>'date-start',
	'value'=>$dateStart,
    // additional javascript options for the date picker plugin
    'options'=>array(
		'changeMonth'=>true,
		'changeYear'=>true,
		'showOtherMonths'=>true,
        'showAnim'=>'fold',
		'dateFormat'=>'yy-mm-dd',
		'onSelect'=>"js:function(selectedDate) {
			var option = this.id == 'date-start' ? 'minDate' : 'maxDate',
				instance = $(this).data('datepicker'),
				date = $.datepicker.parseDate(
					instance.settings.dateFormat ||
					$.datepicker._defaults.dateFormat,
					selectedDate, instance.settings );
			if (this.id == 'date-start') {
				$('#date-end').datepicker('option', option, date);
			} else {
				$('#date-start').datepicker('option', option, date);
			}
		}",
		'onClose'=>"js:function(dateText, inst) {
			if (dateText != '') {
				$('input[name=date-filter][value=custom]').attr('checked', true);
			}
		}",
    ),
	'htmlOptions'=>array('size'=>10),
));
?> to 
<?php
$this->widget('zii.widgets.jui.CJuiDatePicker', array(
    'name'=>'date-end',
	'id'=>'date-end',
	'value'=>$dateEnd,
    // additional javascript options for the date picker plugin
    'options'=>array(
		'changeMonth'=>true,
		'changeYear'=>true,
		'showOtherMonths'=>true,
        'showAnim'=>'fold',
		'dateFormat'=>'yy-mm-dd',
		'onSelect'=>"js:function(selectedDate) {
			var option = this.id == 'date-start' ? 'minDate' : 'maxDate',
				instance = $(this).data('datepicker'),
				date = $.datepicker.parseDate(
					instance.settings.dateFormat ||
					$.datepicker._defaults.dateFormat,
					selectedDate, instance.settings );
			if (this.id == 'date-start') {
				$('#date-end').datepicker('option', option, date);
			} else {
				$('#date-start').datepicker('option', option, date);
			}
		}",
		'onClose'=>"js:function(dateText, inst) {
			if (dateText != '') {
				$('input[name=date-filter][value=custom]').attr('checked', true);
			}
		}"
    ),
	'htmlOptions'=>array('size'=>10),
));
?>
</div>
<button type="submit" value="submit" class="shinybutton highlighted"><span>Search</span></button>
<?php $this->endWidget();
if (!empty($theatres)) { ?>
<div class="cleartall"></div>
<?php
	foreach ($theatres as $name => $dates) {?>
<h3><?php echo $name; ?></h3>
<?php	foreach ($dates as $date => $sessions) { ?>
<table class="theatreList">
<tr>
	<th colspan="6"><?php echo $date; ?></th>
</tr>
<?php		$sessionHeader = false;
			foreach ($sessions as $session) {
				if (!$sessionHeader) {?>
<tr>
	<th colspan="3" style="text-align: center;">Session: <?php echo substr($session['startTime'], 0, 5) . 
		' ' . substr($session['endTime'],0,5); ?></th>
	<th colspan="3">Time unallocated: <?php 
					echo '<span';
					if ($session['timeAvailable'] < 0) {
						echo ' class="full"';
					}
					echo ">{$session['timeAvailable']}"; ?>min</span></th>
</tr>
<tr>
	<th>Patient (Age)</th>
	<th>[Eye] Operation</th>
	<th>Duration</th>
	<th>Ward</th>
	<th>Anaesthetic</th>
	<th>Alerts</th>
</tr>
<?php			
					$sessionHeader = true;
				} ?>
<tr>
	<td><?php echo $session['patientName'] . ' (' . $session['patientAge'] . ')'; ?></td>
	<td>[<?php echo $session['eye']; ?>] <?php echo !empty($session['procedures']) ? $session['procedures'] : 'No procedures'; ?></td>
	<td><?php echo $session['operationDuration']; ?></td>
	<td><?php echo $session['ward']; ?></td>
	<td><?php echo $session['anaesthetic']; ?></td>
	<td><div class="alert gender invisible <?php echo $session['patientGender']; ?>"></div><?php
		if (!empty($session['operationComments'])) { ?><div class="alert comments invisble"><img class="invisible" src="/images/icon_comments.gif" alt="comments" title="<?php echo $session['operationComments']; ?>" /></div><?php
		} ?></td>
</tr>
<?php
			}
		} ?>
</table>
<?php
	} ?>
<div id="alertOptions">
	<input type="checkbox" name="theatre_alerts" value="comments" /> Comments<br />
	<input type="checkbox" name="theatre_alerts" value="gender" /> Gender
</div>
<div class="clear"></div>
<script type="text/javascript">
	$('input[name=theatre_alerts][value=comments]').click(function() {
		if ($(this).is(':checked')) {
			$('.comments').removeClass('invisible');
			$('.comments img').removeClass('invisible');
		} else {
			$('.comments').addClass('invisible');
			$('.comments img').addClass('invisible');
		}
	});
	$('input[name=theatre_alerts][value=gender]').click(function() {
		if ($(this).is(':checked')) {
			$('.gender').removeClass('invisible');
		} else {
			$('.gender').addClass('invisible');
		}
	});
</script>
<?php
} ?>
</div>
<script type="text/javascript">
	$('input[name=date-filter]').change(function() {
		if ($(this).val() != 'custom') {
			$('input[id=date-start]').val('');
			$('input[id=date-end]').val('');
		}
	});
</script>