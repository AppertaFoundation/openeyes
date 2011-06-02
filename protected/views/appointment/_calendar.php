		<span style="width: 100%">
<?php	$lastMonth = date('Y-m-d', mktime(0,0,0, date('m', $date)-1, date('d', $date), date('Y', $date)));
		$nextMonth = date('Y-m-d', mktime(0,0,0, date('m', $date)+1, date('d', $date), date('Y', $date)));
		echo CHtml::form(array('appointment/sessions', 
			'operation'=>$operation->id, 'date'=>$lastMonth),
			'post');
		echo CHtml::hiddenField('operation', $operation->id);
		echo CHtml::hiddenField('pmonth', $lastMonth);
		echo CHtml::submitButton('< Previous Month', array('id' => 'previous_month'));
		echo CHtml::closeTag('form'); ?>
		<strong><?php echo date('F Y', $date); ?></strong>
<?php	echo CHtml::form(array('appointment/sessions', 
			'operation'=>$operation->id, 'date'=>$nextMonth),
			'post');
		echo CHtml::hiddenField('operation', $operation->id);
		echo CHtml::hiddenField('nmonth', $nextMonth);
		echo CHtml::submitButton('Next Month >', array('id' => 'next_month'));
		echo CHtml::closeTag('form'); ?>
		</span><br />
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