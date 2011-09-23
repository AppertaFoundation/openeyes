<?php
/*
_____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk   info@openeyes.org.uk
--
*/

Yii::app()->clientScript->scriptMap['jquery.js'] = false; ?>
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
		echo '<span class="button">';
		echo CHtml::submitButton('< Previous Month',
			array('id' => 'previous_month', 'class'=>'form_button', 'disabled' => (date('Ym') >= date('Ym', $date))));
		echo '</span>';
		echo CHtml::closeTag('form'); ?>
			</div>
			<div id="month_forward" class="column">
<?php	echo CHtml::form(array('booking/sessions',
			'operation'=>$operation->id, 'date'=>$nextMonth),
			'post');
		echo CHtml::hiddenField('operation', $operation->id);
		echo CHtml::hiddenField('nmonth', $nextMonth);
		echo '<span class="button">';
		echo CHtml::submitButton('Next Month >',
			array('id' => 'next_month', 'class'=>'form_button', 'disabled' => ($nextMonth > $nextYear)));
		echo '</span>';
		echo CHtml::closeTag('form'); ?>
			</div>
		</div>
		<table>
			<tbody>
<?php
	$rttDate = strtotime('+11 weeks', strtotime($operation->event->datetime));
	foreach ($sessions as $weekday => $list) { ?>
				<tr>
					<th><?php echo substr($weekday, 0, 3); ?></th>
<?php	foreach ($list as $date => $data) {
			// check if date is outside this month
			if (date('m', strtotime($date)) !== date('m', strtotime($thisMonth))) {
				$list[$date]['status'] = 'invalid';
			} elseif (date('Y-m-d', strtotime($date)) >= date('Y-m-d', $rttDate)) {
				$list[$date]['status'] .= ' outside_rtt';
			} ?>
					<td class="<?php echo $list[$date]['status'];
			if (!empty($operation->booking) &&
				$operation->booking->session->date == $date) {
				echo ' selected_date';
			} ?>"><?php echo date('j', strtotime($date)); ?></td>
<?php	} ?>
				</tr>
<?php
	} ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="8">
						<div id="key">
						<span>Key:</span>
							<div id="available" class="container"><div class="color_box"></div><div class="label">Slots Available</div></div>
							<div id="limited" class="container"><div class="color_box"></div><div class="label">Limited Slots</div></div>
							<div id="full" class="container"><div class="color_box"></div><div class="label">Full</div></div>
							<div id="closed" class="container"><div class="color_box"></div><div class="label">Theatre Closed</div></div>
							<div id="selected_date" class="container"><div class="color_box"></div><div class="label">Selected Date</div></div>
							<div id="outside_rtt" class="container"><div class="color_box"></div><div class="label">Outside RTT</div></div>
						</div>
					</td>
				</tr>
			</tfoot>
		</table>