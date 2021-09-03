<?php
/**
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */ ?>
<header class="element-header">
    <h3 class="element-title">Attachments</h3>
</header>
<div class="element-data full-width">
    <div class="cols-10">
        <?php if ($associated_content) { ?>
            <table class="last-left">
                <thead>
                <tr>
                    <th>Attachment type</th>
                    <th>Attachment name in letter</th>
                    <th>Event Date</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($associated_content as $ac) {
                    $event = Event::model()->findByPk($ac->associated_event_id);
                    $event_name = $event->eventType->name;
                    $event_date = Helper::convertDate2NHS($event->event_date);
                    ?>
                    <tr>
                        <td><?= $event_name ?></td>
                        <td><?= $ac->display_title ?></td>
                        <td><?= $event_date ?></td>
                        <td>
                            <?php if ($ac->associated_protected_file_id) { ?>
                                <i class="oe-i tick-green small pad-right"></i>Attached
                            <?php } else {
                                $tooltip_content = 'Temporary error, please try again. If the error still occurs, please contact support.';
                                ?>
                                <i class="oe-i cross-red small pad-right"></i>Unable to attach
                                <i class="oe-i oe-i info small pad js-has-tooltip" data-tooltip-content="<?= $tooltip_content ?>"></i>
                            <?php } ?>
                        </td>
                    </tr>

                <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            None
        <?php } ?>
    </div>
</div>