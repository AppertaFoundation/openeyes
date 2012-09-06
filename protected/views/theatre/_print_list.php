<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

?>
<div id="diaryTemplate">
<div id="d_title">TCIs in date range <?php echo $_POST['date-start']?> to <?php echo $_POST['date-end']?></div>
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
		<?php foreach ($bookings as $booking) { ?>
			<tr>
				<td><?php echo $booking['hos_num']?></td>
				<td><strong><?php echo strtoupper($booking['last_name']) ?></strong>, <?php echo $booking['first_name']?></td>
				<td><?php echo Helper::convertMySQL2NHS($booking['dob'])?></td>
				<td><?php echo Helper::getAge($booking['dob'])?></td>
				<td><?php echo $booking['gender']?></td>
				<td><?php echo Helper::convertMySQL2NHS($booking['date'])?></td>
				<td><?php echo $booking['ward_name']?></td>
				<td><?php echo $booking['consultant']?></td>
				<td><?php echo $booking['subspecialty']?></td>
			</tr>
		<?php } ?>
	</table>
</div>
