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

$medications = $current ? $patient->medications : $patient->previous_medications;
?>
<?php if ($medications): ?>
	<h4><?= $current ? "Current" : "Previous" ?></h4>
	<?php foreach ($medications as $medication): ?>
		<table class="plain patient-data">
			<tr><th width="128">Medication</th><td><?= $medication->drug->label ?></td></tr>
			<tr>
				<th>Administration</th>
				<td><?= $medication->dose ?> <?= $medication->route->name?> <?= $medication->option ? "({$medication->option->name})" : "" ?> <?= $medication->frequency->name?></td>
			</tr>
			<tr>
				<th>Date</th>
				<td>
					<?php
						echo Helper::formatFuzzyDate($medication->start_date) . " - ";
						if (!$current) {
							echo Helper::formatFuzzyDate($medication->end_date);
							if ($medication->stop_reason) echo " (reason for stopping: {$medication->stop_reason->name})";
						}
					?>
				</td>
			</tr>
			<?php if ($this->checkAccess('OprnEditMedication')): ?>
				<tr>
					<th>Actions</th>
					<td>
						<a href="#" class="medication_edit" data-id="<?= $medication->id ?>">Edit</a> |
						<?php if ($current) { ?><a href="#" class="medication_stop" data-id="<?= $medication->id ?>" data-drug-name="<?= $medication->drug->label ?>">Stop</a> |<?php } ?>
						<a href="#" class="medication_delete" data-id="<?= $medication->id ?>">Delete</a>
					</td>
				</tr>
			<?php endif ?>
		</table>
	<?php endforeach ?>
<?php endif ?>
