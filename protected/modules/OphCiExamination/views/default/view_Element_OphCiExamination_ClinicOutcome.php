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
$row_count = 0;
?>
<div class="element-data full-width">
    <div class="data-group">
        <table class="cols-full">
            <tbody>
            <?php foreach ($element->entries as $entry) { ?>
                <tr>
                    <td>
                        <?= $row_count ? "AND" : "" ?>
                    </td>
                    <td class="large-text" style="text-align:left">
                    <?php
                    if ($entry->isPatientTicket()) {
                        $api = Yii::app()->moduleAPI->get('PatientTicketing');
                        $ticket = $api->getTicketForEvent($this->event);
                        if ($ticket) {
                            if ($ticket->priority) {?>
                            <div class="priority">
                                <span class="highlighter <?= $ticket->priority->colour ?>"><?= $ticket->priority->name ?></span>
                            </div>
                            <?php } ?>
                            <div class="cols-7">
                                <?php $this->widget($api::$TICKET_SUMMARY_WIDGET, array('ticket' => $ticket)); ?>
                            </div>
                        <?php }
                    } else {
                        echo $entry->getInfos();
                    }
                    $row_count++;
                    ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?php if ($element->comments) { ?>
        <div class="data-group">
            <span class="large-text">
                <?= $element->getAttributeLabel('comments') ?>:
                <?= Yii::app()->format->Ntext($element->comments); ?>
            </span>
        </div>
    <?php } ?>

</div>
