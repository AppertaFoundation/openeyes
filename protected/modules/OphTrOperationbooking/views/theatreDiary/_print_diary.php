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
?>
<?php
$diary_count = count($diary) - 1;
foreach ($diary as $i => $theatre) {?>
	<div<?php if ($i < $diary_count) { ?> style="page-break-after:always"<?php } ?>>
	<h3 class="theatre"><strong><?php echo $theatre->name?> (<?php echo $theatre->site->name?>)</strong></h3>
	<?php
    $sessions_count = count($theatre->sessions) - 1;
    foreach ($theatre->sessions as $j => $session) {?>
		<div id="diaryTemplate"
			 <?php if ($j < $sessions_count) { ?>
				 style="page-break-after:always" 
			 <?php } ?>
		>
			<div id="d_title">OPERATION LIST FORM</div>
			<table class="d_overview">
				<tbody>
					<tr>
						<td>THEATRE NO:</td>
						<td colspan="2"><?php echo htmlspecialchars($theatre->name, ENT_QUOTES)?></td>
					</tr>
					<tr>
						<td>SESSION:</td>
						<td><?php echo $session->start_time?> - <?php echo $session->end_time?></td>
						<td>NHS</td>
					</tr>
				</tbody>
			</table>
			<table class="d_overview">
				<tbody>
					<tr>
						<td>SURGICAL FIRM:<?php echo htmlspecialchars($session->firmName, ENT_QUOTES)?></td>
						<td>ANAESTHETIST:</td>
						<td>&nbsp;</td>
						<td>DATE:</td>
						<td><?php echo Helper::convertDate2NHS($session->date)?></td>
					</tr>
					<tr>
						<td>COMMENTS: <?php echo CHtml::encode($session->comments)?></td>
					</tr>
				</tbody>
			</table>
			<table class="d_data">
				<tbody>
					<tr>
						<th>HOSPT NO</th>
						<th>PATIENT</th>
						<th>AGE</th>
						<th>WARD</th>
						<th>GA or LA</th>
						<th>PRIORITY</th>
						<th>PROCEDURES AND COMMENTS</th>
						<th>ADMISSION TIME</th>
					</tr>
					<?php foreach ($session->getActiveBookingsForWard($ward_id) as $booking) {
                        if ($booking->operation->event) { ?>
							<tr>
								<td><?php echo $booking->operation->event->episode->patient->hos_num?></td>
								<td><?php echo strtoupper($booking->operation->event->episode->patient->last_name)?>, <?php echo $booking->operation->event->episode->patient->first_name?></td>
								<td><?php echo $booking->operation->event->episode->patient->age?></td>
								<td><?php echo $booking->ward ? htmlspecialchars($booking->ward->name) : 'None'?></td>
								<td><?php echo htmlspecialchars($booking->operation->getAnaestheticTypeDisplay())?></td>
								<td><?php echo $booking->operation->priority->name?></td>
								<td style="max-width: 500px; word-wrap:break-word; overflow: hidden;">
								<?php echo $booking->operation->procedures ? '['.$booking->operation->eye->adjective.'] '.$booking->operation->getProceduresCommaSeparated() : 'No procedures'?><br/>
								<?php echo CHtml::encode($booking->operation->comments)?>
								<td><?php echo substr($booking->admission_time, 0, 5)?></td>
							</tr>
						<?php }
                    } ?>
				</tbody>
			</table>
		</div>
	<?php } ?>
	</div>
<?php }
