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
<table>
	<thead>
		<tr>
			<th><?php echo Patient::model()->getAttributeLabel('hos_num')?></th>
			<th><?php echo Patient::model()->getAttributeLabel('dob')?></th>
			<th><?php echo Patient::model()->getAttributeLabel('first_name')?></th>
			<th><?php echo Patient::model()->getAttributeLabel('last_name')?></th>
			<th>Date</th>
			<th>Diagnoses</th>
		</tr>
	<tbody>
		<?php if (empty($report->diagnoses)) {?>
			<tr>
				<td colspan="6">
					No patients were found with the selected search criteria.
				</td>
			</tr>
		<?php }else{?>
			<?php foreach ($report->diagnoses as $ts => $diagnosis) {?>
				<tr>
					<td><?php echo $diagnosis['hos_num']?></td>
					<td><?php echo $diagnosis['dob'] ? date('j M Y', strtotime($diagnosis['dob'])) : 'Unknown'?></td>
					<td><?php echo $diagnosis['first_name']?></td>
					<td><?php echo $diagnosis['last_name']?></td>
					<td><?php echo date('j M Y', $ts)?></td>
					<td>
						<?php
                        $_diagnosis = array_shift($diagnosis['diagnoses']);
    echo $_diagnosis['eye'].' '.$_diagnosis['disorder'].' ('.$_diagnosis['type'].')';
    ?>
					</td>
				</tr>
				<?php foreach ($diagnosis['diagnoses'] as $_diagnosis) {?>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td><?php echo date('j M Y', strtotime($_diagnosis['date']))?></td>
						<td>
							<?php echo $_diagnosis['eye'].' '.$_diagnosis['disorder'].' ('.$_diagnosis['type'].')'?>
						</td>
					</tr>
				<?php }?>
			<?php }?>
		<?php }?>
	</tbody>
</table>
