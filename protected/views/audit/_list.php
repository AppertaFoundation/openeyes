<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<div class="row">
	<div class="large-12 column">
		<h2>Results:</h2>
	</div>
</div>

<div class="row">
	<div class="large-12 column">
		<div class="box generic">
			<?php
			if (empty($data['items'])) {?>
				<div class="alert-box">
					No audit logs match the search criteria.
				</div>
			<?php
			} else {?>
				<div class="pagination"></div>
				<table class="grid audit-logs">
					<thead>
						<tr>
							<th>Timestamp</th>
							<th>Site</th>
							<th>Firm</th>
							<th>User</th>
							<th>Action</th>
							<th>Target type</th>
							<th>Event type</th>
							<th>Patient</th>
							<th>Episode</th>
						</tr>
					</thead>
					<tbody id="auditListData">
						<?php foreach ($data['items'] as $i => $log) {
							$this->renderPartial('_list_row',array('i'=>$i,'log'=>$log));
						}?>
					</tbody>
				</table>
				<div class="pagination last"></div>
			<?php }?>
		</div>
	</div>
</div>