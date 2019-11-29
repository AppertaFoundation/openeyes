<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
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

if (!empty($dispense_locations)) {
    foreach ($dispense_locations as $i => $dispense_location) {?>
        <tr class="clickable"
            data-id="<?php echo $dispense_location->id?>"
            data-uri="OphDrPrescription/oeadmin/DispenseLocation/edit/<?php echo $dispense_location->id?>"
        >
            <td class="reorder">
                <span>↑↓</span>
                <?=\CHtml::activeHiddenField($dispense_location, "[$i]display_order");?>
                <?=\CHtml::activeHiddenField($dispense_location, "[$i]id");?>
            <td><?php echo $dispense_location->name?></td>
            <td><?php echo $dispense_location->display_order?></td>
            <td><i class="oe-i <?=($dispense_location->active ? 'tick' : 'remove');?> small"></i></td>
        </tr>
    <?php }
}