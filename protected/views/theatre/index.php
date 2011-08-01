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
	echo CHtml::dropDownList('site-id', '', Site::model()->getList(), 
		array('empty'=>'Select a site', 'ajax'=>array(
			'type'=>'POST',
			'data'=>array('site_id'=>'js:this.value'),
			'url'=>Yii::app()->createUrl('theatre/filterTheatres'),
			'success'=>"js:function(data) {
				$('#theatre-id').html(data);
			}",
		))); ?>
	<strong>Service:</strong>
<?php
	echo CHtml::dropDownList('service-id', '', Service::model()->getList(), 
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
	echo CHtml::dropDownList('firm-id', '', array(), 
		array('empty'=>'All firms', 'disabled'=>true)); ?>
	<strong>Theatre:</strong>
<?php
	echo CHtml::dropDownList('theatre-id', '', array(), 
		array('empty'=>'All theatres')); ?>
</div>
<div>
	<input type="radio" name="date-filter" value="today" /> Today
	<input type="radio" name="date-filter" value="week" /> This week
	<input type="radio" name="date-filter" value="month" /> This month
	<input type="radio" name="date-filter" value="custom" /> or from 
<?php
$this->widget('zii.widgets.jui.CJuiDatePicker', array(
    'name'=>'date-start',
    // additional javascript options for the date picker plugin
    'options'=>array(
        'showAnim'=>'fold',
		'dateFormat'=>'yy-mm-dd',
		'onClose'=>"js:function(dateText, inst) {
			if (dateText != '') {
				$('input[name=date-filter]
			}
		}"
    ),
	'htmlOptions'=>array('size'=>10),
));
?> to 
<?php
$this->widget('zii.widgets.jui.CJuiDatePicker', array(
    'name'=>'date-end',
    // additional javascript options for the date picker plugin
    'options'=>array(
        'showAnim'=>'fold',
		'dateFormat'=>'yy-mm-dd'
    ),
	'htmlOptions'=>array('size'=>10),
));
?>
</div>
<button type="submit" value="submit" class="shinybutton highlighted">Search</button>
<?php $this->endWidget();
if (!empty($theatres)) {
	foreach ($theatres as $name => $dates) {?>
<h3><?php echo $name; ?></h3>
<table style="border: 1px solid #000;" width="100%">
<?php	foreach ($dates as $date => $sessions) { ?>
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
	<td>Ward</td>
	<td><?php echo $session['anaesthetic']; ?></td>
	<td><?php echo $session['operationComments']; ?> / <?php echo $session['patientGender']; ?></td>
</tr>
<?php
			}
		}
	}
} ?>
<p/>
<p/>
<table style="border: 1px solid #000;" width="100%">
<tr>
	<th colspan="6">14 JUL 2011</th>
</tr>
<tr>
	<th colspan="3" style="text-align: center;">Session: 8:30-9:00</th>
	<th colspan="3">Time unallocated: 220min</th>
</tr>
<tr>
	<th>Patient</th>
	<th>Operation</th>
	<th>Duration</th>
	<th>Ward</th>
	<th>Anaesthetic</th>
	<th>Alerts</th>
</tr>
<tr>
	<td>Sharon Day (43)</td>
	<td>Canaliculo DCR</td>
	<td>270</td>
	<td>Observation</td>
	<td>GA</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td>Peter Smith (6)</td>
	<td>BipsyLidLesion</td>
	<td>180</td>
	<td>Childrens</td>
	<td>LA</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td>James Smith (43)</td>
	<td>Canaliculo DCR, BiopsyLidLesion</td>
	<td>220</td>
	<td>Observation</td>
	<td>GA</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<th colspan="3" style="text-align: center;">Session: 13:00-17:00</th>
	<th colspan="3">Time unallocated: 20min</th>
</tr>
<tr>
	<th>Patient</th>
	<th>Operation</th>
	<th>Duration</th>
	<th>Ward</th>
	<th>Anaesthetic</th>
	<th>Alerts</th>
</tr>
<tr>
	<td>Karen Flint (38)</td>
	<td>BipsyLidLesion</td>
	<td>130</td>
	<td>Sedgewick</td>
	<td>LAC</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td>Debbie Carrolls (67)</td>
	<td>Caniculo DCR</td>
	<td>160</td>
	<td>Sedgewick</td>
	<td>TOP</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td>Lucy Cavendish (72)</td>
	<td>BiopsyLidLesion</td>
	<td>60</td>
	<td>Sedgewick</td>
	<td>TOP</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td>Michael Bennett (68)</td>
	<td>Caniculo DCR</td>
	<td>110</td>
	<td>Mackellar</td>
	<td>LAS</td>
	<td>&nbsp;</td>
</tr>
</table>