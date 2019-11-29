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

if (!empty($dispense_conditions)) {
    foreach ($dispense_conditions as $i => $dispense_condition) {?>
        <tr class="clickable"
            data-id="<?php echo $dispense_condition->id?>"
            data-uri="OphDrPrescription/oeadmin/DispenseCondition/edit/<?php echo $dispense_condition->id?>"
        >
            <td class="reorder">
                <span>↑↓</span>
                <?=\CHtml::activeHiddenField($dispense_condition, "[$i]display_order");?>
                <?=\CHtml::activeHiddenField($dispense_condition, "[$i]id");?>
            <td><?php echo $dispense_condition->name?></td>
            <td><?php echo $dispense_condition->display_order?></td>
            <td><i class="oe-i <?=($dispense_condition->active ? 'tick' : 'remove');?> small"></i></td>
        </tr>
    <?php }
}