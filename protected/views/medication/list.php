<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<?php if ($medications): ?>
	<h4><?= $current ? "Current" : "Previous" ?></h4>
	<table class="plain patient-data">
		<thead>
		<tr>
			<th>Medication</th>
			<th>Dose</th>
			<th>Route</th>
			<th>Option</th>
			<th>Frequency</th>
			<th>Start date</th>
			<?php if (!$current): ?>
				<th>End date</th>
				<th>Stop reason</th>
			<?php endif ?>
			<?php if ($this->checkAccess('OprnEditMedication')) { ?><th>Actions</th><?php } ?>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($medications as $medication) {?>
			<tr>
				<td><?= $medication->drug->name ?></td>
				<td><?= $medication->dose ?: '-' ?></td>
				<td><?= $medication->route->name ?></td>
				<td><?= $medication->option ? $medication->option->name : '-' ?></td>
				<td><?= $medication->frequency->name ?></td>
				<td><?= Helper::convertMySQL2NHS($medication->start_date) ?></td>
				<?php if (!$current): ?>
					<td><?= Helper::convertMySQL2NHS($medication->end_date) ?></td>
					<td><?= $medication->stop_reason ? $medication->stop_reason->name : '-' ?></td>
				<?php endif ?>
				<?php if ($this->checkAccess('OprnEditMedication')): ?>
					<td>
						<a href="#" class="editMedication" rel="<?= $medication->id?>">Edit</a>&nbsp;&nbsp;
						<?php if ($current) { ?><a href="#" class="removeMedication" rel="<?= $medication->id?>">Remove</a><?php } ?>
					</td>
				<?php endif ?>
			</tr>
		<?php }?>
		</tbody>
	</table>
<?php endif ?>
