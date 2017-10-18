<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<div class="box admin">
	<h2>Contact location</h2>
	<div class="row data-row">
		<div class="large-2 column">
			<div class="data-label">Contact:</div>
		</div>
		<div class="large-10 column">
			<div class="data-value"><?php echo $location->contact->fullName?></div>
		</div>
	</div>
	<div class="row data-row">
		<div class="large-2 column">
			<div class="data-label"><?php echo $location->site_id ? 'Site' : 'Institution'?>:</div>
		</div>
		<div class="large-10 column">
			<div class="data-value">
				<?php echo $location->site ? $location->site->name : $location->institution->name?>
			</div>
		</div>
	</div>
</div>

<div class="box admin">
	<h2>Patients</h2>
	<form id="admin_contact_patients">
		<table class="grid">
			<thead>
				<tr>
					<th>Hos num</th>
					<th>Title</th>
					<th>First name</th>
					<th>Last name</th>
				</tr>
			</thead>
			<tbody>
				<?php
                foreach ($location->patients as $i => $patient) {?>
					<tr class="clickable" data-id="<?php echo $patient->id?>" data-uri="patient/view/<?php echo $patient->id?>">
						<td><?php echo $patient->hos_num?>&nbsp;</td>
						<td><?php echo $patient->title?>&nbsp;</td>
						<td><?php echo $patient->first_name?>&nbsp;</td>
						<td><?php echo $patient->last_name?>&nbsp;</td>
					</tr>
				<?php }?>
			</tbody>
		</table>
	</form>
</div>