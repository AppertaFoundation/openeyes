<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div class="row">

	<div class="large-12 column">
		<div class="box generic">
			<?php
            if (!count($tickets)) {?>
				<div class="alert-box">
					No tickets match the search criteria.
				</div>
			<?php
            } else {?>
				<div class="pagination">
				</div>
				<table class="grid audit-logs" id="ticket-table">
					<thead>
					<tr>
						<th>Patient List</th>
						<th class="large-2">Patient</th>
						<th>Priority</th>
						<th>Referral Date</th>
						<th>Firm</th>
						<th>Created By</th>
						<th class="large-2">Clinic Info</th>
						<th class="large-2">Referral Notes</th>
						<!--<th>Ticket Owner</th>-->
						<th class="large-2">Actions</th>
					</tr>
					</thead>
					<tbody id="ticket-list">
					<?php foreach ($tickets as $i => $t) {
                        $this->renderPartial('_ticketlist_row', array('i' => $i, 'ticket' => $t, 'can_process' => $can_process));
                    }?>
					</tbody>
				</table>
				<div class="text-center pagination last">
					<?php $this->widget('CLinkPager', array(
                                    'pages' => $pages,
                                    'header' => '',
                            )) ?>
				</div>
			<?php }?>
		</div>
	</div>
</div>