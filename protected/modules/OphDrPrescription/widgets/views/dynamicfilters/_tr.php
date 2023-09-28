<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2023, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<tr class="parameter" data-key="<?=$key?>">
    <td>
        <?=$main_label;?>
        <input type="hidden" value="<?=$main_label?>" name="<?=$parameter_name?>[<?=$key?>][type]" id="<?=$parameter_name?>_<?=$key?>_type">
    </td>
    <td>
        <span class="highlighter">IS <?=$value_label?></span>
        <input name="<?=$parameter_name?>[<?=$key?>][value]" id="<?=$parameter_name?>_<?=$key?>_value" type="hidden" value="<?=$value_label?>">
    </td>
    <td>
        <?=$operation_label?>
        <input name="<?=$parameter_name?>[<?=$key?>][operation]" id="<?=$parameter_name?>_<?=$key?>_operation" type="hidden" value="<?=$operation_label?>">
    </td>
    <td>
        <i data-test="remove-dynamic-filter-<?=$key?>" id="<?=$key?>-remove" class="oe-i remove-circle small"></i>
    </td>
</tr>
