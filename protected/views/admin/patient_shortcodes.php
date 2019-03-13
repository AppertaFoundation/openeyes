<?php
/**
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div class="row divider">
    <h2>Shortcodes</h2>
</div>

<table class="standard comfortable">
    <colgroup>
        <col class="cols-1">
        <col class="cols-1">
        <col class="cols-2">
        <col class="cols-2">
        <col class="cols-2">
        <col class="cols-4">
    </colgroup>
    <thead>
    <tr>
        <th>Code</th>
        <th>Default Code</th>
        <th>Description</th>
        <th>Event Type</th>
        <th>Method</th>
        <th>Code Documentation</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($short_codes as $short_code) : ?>
        <tr>
            <td class="code"><?= $short_code->code; ?></td>
            <td class="default-code"><?= $short_code->default_code; ?></td>
            <td class="description"><?= $short_code->description; ?></td>
            <td class="event-type"><?=(isset($short_code->eventType) ? $short_code->eventType->name : ''); ?></td>
            <td class="method word-break-all">
                <?php
                if (strlen($short_code->method) > 24) {
                    $x = 25;
                    $continue = true;
                    do {
                        $x--;
                        if (ctype_upper($short_code->method[$x]) || is_numeric($short_code->method[$x])) {
                            $continue = false;
                        }
                    } while (($x >= 0) && $continue);

                    echo substr($short_code->method, 0, $x) . "<br>" . substr($short_code->method, $x);
                } else {
                    echo $short_code->method;
                }
                ?>
            </td>
            <td class="code-doc"><?= $short_code->getcodedoc(); ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
