<?php
/**
 * OpenEyes.
 *
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


<table>
    <colgroup>
        <col class="cols-4">
        <col class="cols-4">
        <col class="cols-4">
    </colgroup>
    <thead>
        <tr>
            <th><?php echo $element->getAttributeLabel('site_id') ?></th>
            <th><?php echo $element->getAttributeLabel('laser_id') ?></th>
            <th><?php echo $element->getAttributeLabel('operator_id') ?></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="large-text"><?php echo $element->site ? $element->site->name : 'None' ?></td>
            <td class="large-text"><?php echo $element->laser ? $element->laser->name : 'None' ?></td>
            <td class="large-text"><?php echo $element->surgeon ? $element->surgeon->ReversedFullName : 'None' ?></td>
        </tr>
    </tbody>
</table>

