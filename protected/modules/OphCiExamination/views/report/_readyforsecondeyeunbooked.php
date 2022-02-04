<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>
<table class="standard">
    <thead>
    <tr>
        <th><?= Event::model()->getAttributeLabel('event_date'); ?></th>
        <th><?= $report->getPatientIdentifierPrompt() ?></th>
        <th><?= $report->getAttributeLabel('all_ids') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php if (empty($report->items)) { ?>
        <tr>
            <td colspan="6">
                No records were found.
            </td>
        </tr>
    <?php } else { ?>
        <?php foreach ($report->items as $item) { ?>
            <tr>
                <td><?= date('j M Y', strtotime($item['event_date'])) ?></td>
                <td><?= $item['identifier'] ?></td>
                <td><?= $item['all_ids'] ?></td>
            </tr>
        <?php } ?>
    <?php } ?>
    </tbody>
</table>
