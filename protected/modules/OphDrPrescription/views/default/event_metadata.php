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
<?php
$event = $this->event;
$event_type = $event->eventType->name;
?>
<div class="metadata">
    <?php if (!@$hide_created) { ?>
        <span class="info">
			<?php echo $event_type ?> created by <span class="user"><?php echo $event->user->fullname ?></span>
			on <?php echo $event->NHSDate('created_date') ?>
            at <?php echo date('H:i', strtotime($event->created_date)) ?>
		</span>
    <?php } ?>
    <?php if (!@$hide_modified) { ?>
        <span class="info">
			<?php echo $event_type ?> last modified by <span class="user"><?php echo $event->usermodified->fullname ?></span>
			on <?php echo $event->NHSDate('last_modified_date') ?>
            at <?php echo date('H:i', strtotime($event->last_modified_date)) ?>.
            <?php if(!is_null($Element->edit_reason_id)): ?>
                <?php if($Element->edit_reason_id > 1): ?>
                        Reason: <?php echo $Element->edit_reason->caption; ?>
                    <?php else: ?>
                        Reason: <?php echo $Element->edit_reason_other; ?>
                    <?php endif; ?>
            <?php endif; ?>

		</span>
    <?php } ?>
</div>
