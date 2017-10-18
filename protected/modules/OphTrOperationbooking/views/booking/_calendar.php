<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
$thisMonth = date('Y-m-d', $date);
$lastMonth = mktime(0, 0, 0, date('m', $date) - 1, 1, date('Y', $date));
$nextMonth = mktime(0, 0, 0, date('m', $date) + 1, 1, date('Y', $date));
$nextYear = mktime(0, 0, 0, date('m'), 1, date('Y') + 1);
?>
<div id="dates" class="clearfix">
	<div id="current_month" class="column"><?php echo date('F Y', $date)?></div>
	<div class="left" id="month_back">
		<div class="primary" id="previous_month">
			<?php echo CHtml::link('&#x25C0;&nbsp;&nbsp;previous month',
                array('booking/'.($operation->booking ? 're' : '').'schedule/'.$operation->event_id.'?firm_id='.($firm->id ? $firm->id : 'EMG').'&date='.date('Ym', $lastMonth)),
                array('class' => 'button primary')
            )?>
		</div>
	</div>
	<div class="right" id="month_forward">
		<div id="next_month">
			<?php if ($nextMonth > $nextYear) {
    echo '<a href="#" class="button primary disabled" id="next_month">next month&nbsp;&nbsp;&#x25B6;</a>';
            } else {?>
				<?php echo CHtml::link('<span class="button-span button-span-blue">next month&nbsp;&nbsp;&#x25B6;</span>',
                    array('booking/'.($operation->booking ? 're' : '').'schedule/'.$operation->event_id.'?firm_id='.($firm->id ? $firm->id : 'EMG').'&date='.date('Ym', $nextMonth)),
                    array('class' => 'button primary')
                )?>
			<?php }?>
		</div>
	</div>
</div>
<table id="calendar">
	<tbody>
		<?php
        foreach ($sessions as $weekday => $list) {?>
			<tr>
				<th><?php echo $weekday?></th>

				<?php foreach ($list as $date => $session) {?>
					<?php if ($session['status'] == 'blank') {?>
						<td></td>
					<?php } else {?>
						<td class="<?php echo $session['status']?><?php if ($date == $selectedDate) {?> selected_date<?php }?>">
							<?php echo date('j', strtotime($date))?>
						</td>
					<?php }?>
				<?php }?>
			</tr>
		<?php }?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="8">
				<div id="key">
				<span>Key:</span>
					<div class="container" id="day"><div class="color_box"></div><div class="label">Day of the week</div></div>
					<div class="container" id="available"><div class="color_box"></div><div class="label">Slots Available</div></div>
					<div class="container" id="limited"><div class="color_box"></div><div class="label">Limited Slots</div></div>
					<div class="container" id="full"><div class="color_box"></div><div class="label">Full</div></div>
					<div class="container" id="closed"><div class="color_box"></div><div class="label">Theatre Closed</div></div>
					<div class="container" id="selected_date"><div class="color_box"></div><div class="label">Selected Date</div></div>
					<?php if ($operation->getRTTBreach()) {?>
						<div class="container" id="outside_rtt"><div class="color_box"></div><div class="label">Outside RTT</div></div>
					<?php } ?>
					<div class="container" id="patient-unavailable"><div class="color_box"></div><div class="label">Patient Unavailable</div></div>
				</div>
			</td>
		</tr>
	</tfoot>
</table>
