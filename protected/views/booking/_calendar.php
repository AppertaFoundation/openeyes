		<div id="dates">
<?php	$today = date('Y-m-d');
		$thisMonth = date('Y-m-d', $date);
		$lastMonth = date('Y-m-d', mktime(0,0,0, date('m', $date)-1, 1, date('Y', $date)));
		$nextMonth = date('Y-m-d', mktime(0,0,0, date('m', $date)+1, 1, date('Y', $date)));
		$nextYear = date('Y-m-d', mktime(0,0,0, date('m'), 1, date('Y')+1)); ?>
			<div id="current_month" class="column"><?php echo date('F Y', $date); ?></div>
			<div id="month_back" class="column">
<?php	echo CHtml::form(array('booking/sessions',
			'operation'=>$operation->id, 'date'=>$lastMonth),
			'post');
		echo CHtml::hiddenField('operation', $operation->id);
		echo CHtml::hiddenField('pmonth', $lastMonth);
		echo CHtml::submitButton('< Previous Month', 
			array('id' => 'previous_month', 'disabled' => ($today > $thisMonth)));
		echo CHtml::closeTag('form'); ?>
			</div>
			<div id="month_forward" class="column">
<?php	echo CHtml::form(array('booking/sessions',
			'operation'=>$operation->id, 'date'=>$nextMonth),
			'post');
		echo CHtml::hiddenField('operation', $operation->id);
		echo CHtml::hiddenField('nmonth', $nextMonth);
		echo CHtml::submitButton('Next Month >', 
			array('id' => 'next_month', 'disabled' => ($nextMonth > $nextYear)));
		echo CHtml::closeTag('form'); ?>
			</div>
		</div>
		<table>
			<tbody>
<?php
	foreach ($sessions as $weekday => $list) { ?>
				<tr>
					<th><?php echo $weekday; ?></th>
<?php	foreach ($list as $date => $data) { ?>
					<td class="<?php echo $list[$date]['status']; ?>"><?php echo date('j', strtotime($date)); ?></td>
<?php	} ?>					
				</tr>
<?php
	} ?>
			</tbody>
		</table>