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
	<div class="row">
		<div class="large-8 column">
			<h2>Event deletion requests</h2>
		</div>
		<div class="large-4 column">
			<?php
            $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
                'id' => 'searchform',
                'enableAjaxValidation' => false,
                'focus' => '#search',
                'action' => Yii::app()->createUrl('/admin/eventDeletionRequests'),
            ))?>
			<?php $this->endWidget()?>
		</div>
	</div>
	<form id="admin_event_deletion_requests">
		<input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken?>" />
		<table class="grid">
			<thead>
				<tr>
					<th>Date/time</th>
					<th>User</th>
					<th>Event</th>
					<th>Reason</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php
                foreach ($events as $i => $event) {?>
					<tr data-id="<?php echo $event->id?>" data-uri="admin/viewDeletionRequest/<?php echo $event->id?>">
						<td><?php echo $event->NHSDate('last_modified_date')?> <?php echo substr($event->last_modified_date, 11, 5)?></td>
						<td><?php echo $event->usermodified->fullName?></td>
						<td><a href="<?php echo Yii::app()->createUrl('/'.$event->eventType->class_name.'/default/view/'.$event->id)?>"><?php echo $event->eventType->name?> <?php echo $event->id?></a></td>
						<td><?php echo $event->delete_reason?></td>
						<td>
							<?php echo EventAction::button('Approve', 'approve', null, array('class' => 'small'))->toHtml()?>
							<?php echo EventAction::button('Reject', 'reject', null, array('class' => 'small'))->toHtml()?>
						</td>
					</tr>
				<?php }?>
			</tbody>
		</table>
	</form>
</div>
