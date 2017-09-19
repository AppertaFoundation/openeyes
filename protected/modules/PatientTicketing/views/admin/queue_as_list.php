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

<li class="queue<?= $queue->active ? '' : ' inactive'?>">
	<div class="description"><?= $queue->name ?></div>
	<div class="actions" data-queue-id="<?=$queue->id?>">
		<span class="edit admin-action has-tooltip" data-tooltip="edit">e</span> -
		<span class="add-child admin-action has-tooltip" data-tooltip="add">+</span> -
		<span class="active-toggle admin-action has-tooltip" data-tooltip="<?= $queue->active ? 'deactivate' : 'activate' ?>"><?= $queue->active ? 'x' : 'o' ?></span> -
		<span class="expansion-controls">
			<span class="show-children has-tooltip" data-tooltip="expand" style="display:none;">\/</span>
			<span class="hide-children has-tooltip" data-tooltip="collapse">/\</span>
		</span>
	</div>

	<?php if ($queue->all_outcome_queues) {?>
		<ul>
			<?php foreach ($queue->all_outcome_queues as $oc) { ?>
				<?php $this->renderPartial('queue_as_list', array('queue' => $oc)); ?>
			<?php }?>
		</ul>
	<?php } ?>

</li>