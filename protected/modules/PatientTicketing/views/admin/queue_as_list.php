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

<li class="queue<?= $queue->active ? '' : ' inactive'?>" style="color:white">
    <div class="description"><?= $queue->name ?></div>
    <div class="actions" data-queue-id="<?=$queue->id?>">
        <span class="edit admin-action js-has-tooltip" data-tooltip-content="edit"><i class="oe-i small pencil pro-theme"></i></span> -
        <span class="add-child admin-action js-has-tooltip" data-tooltip-content="add"><i class="oe-i small plus-circle pro-theme"></i></span> -
        <span class="active-toggle admin-action js-has-tooltip" data-tooltip-content="<?= $queue->active ? 'deactivate' : 'activate' ?>"><?= $queue->active ? '<i class="oe-i small remove pro-theme"></i>' : 'o' ?></span> -
        <span class="expansion-controls">
            <span class="show-children js-has-tooltip" data-tooltip-content="expand" style="display:none;"><i class="oe-i small expand pro-theme"pro-theme"></i></span>
            <span class="hide-children js-has-tooltip" data-tooltip-content="collapse"><i class="oe-i small collapse pro-theme"></i></span>
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