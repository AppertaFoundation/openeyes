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

<tr class="history" data-ticket-id="<?= $ass->ticket->id ?>">
    <td><i class="oe-i child-arrow medium no-click"></i></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td><?= Helper::convertDate2NHS($ass->assignment_date) ?></td>
    <td>
        <?= $ass->assignment_firm->getNameAndSubspecialty() ?>
        <br>
        <small class="fade"><?= $ass->assignment_user->getFullName() ?></small>
    </td>
    <td>
        <div class="clinic-info scroll-content">
            <?= $ass->report ?>
        </div>
    </td>
    <td>
        <div class="scroll-content">
            <?= Yii::app()->format->Ntext($ass->notes) ?>
        </div>
    </td>
    <td>&nbsp;</td>
</tr>