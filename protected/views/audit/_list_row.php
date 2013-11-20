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
<tr class="<?php echo ($i % 2 == 0) ? 'even' : 'edd'; echo " ".strtolower($log->colour);?>" id="audit<?php echo $log->id?>"<?php if (@$hidden) {?> style="display: none;"<?php }?>>
	<td>
		<a href="#" id="auditItem<?php echo $log->id?>" class="auditItem">
			<?php echo $log->NHSDate('created_date').' '.substr($log->created_date,11,8)?>
		</a>
	</td>
	<td><?php echo $log->site ? ($log->site->short_name ? $log->site->short_name : $log->site->name) : '-'?></td>
	<td><?php echo $log->firm ? $log->firm->name : '-'?></td>
	<td><?php echo $log->user ? $log->user->first_name.' '.$log->user->last_name : '-'?></td>
	<td><?php echo $log->action->name?></td>
	<td><?php echo $log->target_type ? $log->target_type->name : ''?></td>
	<td>
		<?php if ($log->event) { ?>
			<a href="/<?php echo $log->event->eventType->class_name?>/default/view/<?php echo $log->event_id?>">
				<?php echo $log->event->eventType->name?>
			</a>
		<?php } else {?>
			-
		<?php }?>
	</td>
	<td>
		<?php if ($log->patient) {?>
			<?php echo CHtml::link($log->patient->displayName,array('patient/view/'.$log->patient_id))?>
		<?php } else {?>
			-
		<?php }?>
	</td>
	<td>
		<?php if ($log->episode) {?>
			<?php echo CHtml::link('view',array('patient/episode/'.$log->episode_id))?>
		<?php } else {?>
			-
		<?php }?>
	</td>
</tr>
<tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; echo " ".strtolower($log->colour);?> auditextra<?php echo $log->id?>" style="display: none;">
	<td colspan="9">
		<div class="panel logs">
			<table class="blank plain log-details">
				<tr>
					<th scope="col">IP address:</th>
					<td><?php echo $log->ip_addr ? $log->ip_addr->name : '-'?></td>
				</tr>
				<tr>
					<th scope="col">Server name:</th>
					<td><?php echo $log->server ? $log->server->name : '-' ?></td>
				</tr>
				<tr>
					<th scope="col">Request URI:</th>
					<td><?php echo $log->request_uri?></td>
				</tr>
				<tr>
					<th scope="col">User agent:</th>
					<td><?php echo $log->user_agent ? $log->user_agent->name : '-' ?></td>
				</tr>
				<tr>
					<th scope="col">Data:</th>
					<td>
						<?php
						if (@unserialize($log->data)) {?>
							<div class="link">
								<a href="#" id="showData<?php echo $log->id?>" class="showData">show data</a>
								<input type="hidden" name="data<?php echo $log->id?>" value="<?php echo htmlentities($log->data)?>" />
							</div>
							<div class="data" id="dataspan<?php echo $log->id?>"></div>
						<?php } else {?>
							<div class="data">
								<?php echo $log->data ? $log->data : 'None';?>
							</div>
						<?php }?>
					</td>
				</tr>
			</table>
		</div>
	</td>
</tr>