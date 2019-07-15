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
<tr class="colourvisionReading" data-key="<?php echo $key ?>">
    <td>
        <?php if (isset($reading) && $reading->id) {
    ?>
            <input type="hidden" name="<?= $name_stub ?>[<?php echo $key ?>][id]" value="<?php echo $reading->id?>" />
        <?php 
}?>
        <span class="methodName"><?php echo $method_name ?></span>
        <input type="hidden" name="<?= $name_stub ?>[<?php echo $key ?>][method_id]" class="methodId" value="<?php echo @$method_id ?>" />
    </td>
    <td>
        <select name="<?= $name_stub ?>[<?php echo $key ?>][value_id]">
            <?php if (isset($reading)) {
    foreach ($reading->method->values as $v) {
        ?>
                    <option value="<?php echo $v->id?>"<?php if ($v->id == @$reading->value_id) {
    ?> selected="selected"<?php 
}
        ?>><?php echo $v->name?></option>
                <?php 
    }
} else {
    echo $method_values;
}?>
        </select>
    </td>
  <td class="cols-2 readingActions">
    <i class="oe-i trash removeCVReading"></i>
  </td>
</tr>
