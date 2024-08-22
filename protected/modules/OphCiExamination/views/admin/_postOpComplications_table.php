<?php
/**
 * (C) OpenEyes Foundation, 2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<table>
    <colgroup>
        <col class="cols-6">
    </colgroup>

    <thead>
    <th>Item name</th>
    </thead>

    <tbody id="<?= $id ?>">
    <?php
    foreach ($items as $item) { ?>
        <tr class="draggablelist-item" data-item-id="<?= $item->id ?>">
            <td data-test="assigned-item-name"><?= $item->name ?></td>
        </tr>
    <?php } ?>

    <tr class="draggablelist-empty" style="display: none">
        <td class="text-center">(drag items here)</td>
    </tr>

    </tbody>
</table>
