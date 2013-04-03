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
<div id="auditList" class="grid-view">
	<?php
	if (empty($data['items'])) {?>
		<h4>No audit logs match the search criteria.</h4>
	<?php
	} else {
	?>
		<ul id="auditList">
			<li class="header">
				<span class="timestamp">Timestamp</span>
				<span class="site">Site</span>
				<span class="firm">Firm</span>
				<span class="user">User</span>
				<span class="action">Action</span>
				<span class="target">Target type</span>
				<span class="event_type">Event type</span>
				<span class="patient">Patient</span>
				<span class="episode">Episode</span>
			</li>
			<div id="auditListData">
				<?php foreach ($data['items'] as $i => $log) {
					$this->renderPartial('_list_row',array('i'=>$i,'log'=>$log));
				}?>
			</div>
		</ul>
	<?php }?>
</div>
