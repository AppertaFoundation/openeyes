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
<div id="diaryTemplate">
	<div id="d_title">TCIs in date range <?php echo CHtml::encode($_POST['date-start'])?> to <?php echo CHtml::encode($_POST['date-end'])?></div>
	<table>
		<tr>
			<th>Patient no</th>
			<th>Patient name</th>
			<th>D.O.B.</th>
			<th>Age</th>
			<th>Sex</th>
			<th>Op. date</th>
			<th>Ward</th>
			<th>Consultant</th>
			<th>Subspecialty</th>
		</tr>
		<?php foreach ($bookings as $booking) {
            if ($booking->operation->event) { ?>
				<tr>
					<td><?php echo $booking->operation->event->episode->patient->hos_num?></td>
					<td><strong><?php echo strtoupper($booking->operation->event->episode->patient->last_name) ?></strong>, <?php echo $booking->operation->event->episode->patient->first_name?></td>
					<td><?php echo $booking->operation->event->episode->patient->NHSDate('dob')?></td>
					<td><?php echo $booking->operation->event->episode->patient->age?></td>
					<td><?php echo $booking->operation->event->episode->patient->gender?></td>
					<td><?php echo $booking->NHSDate('session_date')?></td>
					<td><?php echo $booking->ward ? $booking->ward->name : 'None'?></td>
					<td><?php echo $booking->session->firm->pas_code?></td>
					<td><?php echo $booking->session->firm->serviceSubspecialtyAssignment->subspecialty->name?></td>
				</tr>
			<?php }
} ?>
	</table>
</div>
