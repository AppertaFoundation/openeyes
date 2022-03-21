<?php
/**
 * (C) OpenEyes Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<tr data-key="<?=$key?>">
    <td class="cols-full">
        Key:
        <?= \CHtml::activeTextField(
            $ldap_config,
            "ldap_additional_params[$key][key]",
        ); ?>
    </td>
    <td>
        Value:
        <?= \CHtml::activeTextField(
            $ldap_config,
            "ldap_additional_params[$key][value]",
        ); ?>
        <i class="oe-i trash js-remove-row"></i>
    </td>
</tr>
