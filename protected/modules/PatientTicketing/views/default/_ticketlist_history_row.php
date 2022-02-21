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

/**
 * @var \OEModule\PatientTicketing\models\TicketQueueAssignment $ass
 */
?>

<tr class="history no-line" data-ticket-id="<?= $ass->ticket->id ?>">
    <td><i class="oe-i child-arrow medium no-click"></i></td>
    <td>&nbsp;</td>
    <td><small class="fade"><?= $ass->ticket->patient->getHSCICName() ?></small></td>
    <td>
        <div class="small-row"><?=$ass->ticket->getTicketFirm();?><br>
            <small class="fade"><?= $ass->ticket->user->getFullName() ?></small>
        </div>
    </td>
    <td>
        <div class="flex-t col-gap">
            <div class="clinic-info scroll-content flex-fill-2">
                <?= $ass->formattedReport ? preg_replace('/^(<br \/>)/', '', $ass->formattedReport) : '-'; ?>
            </div>
            <div class="scroll-content flex-fill">
                <em><?= \Yii::app()->format->Ntext($ass->notes) ?></em>
            </div>
        </div>
    </td>
</tr>
