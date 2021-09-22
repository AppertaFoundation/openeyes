<?php
/**
 * OpenEyes.
 *
 * (C) Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
    <div class="element-data full-width">
        <div class="cols-10">
            <table class="cols-full last-left">
                <colgroup>
                    <col class="cols-6">
                </colgroup>
                <tbody>
                    <tr>
                        <td>
                            <?= CHtml::encode($element->getAttributeLabel('name_hp')) ?>
                        </td>
                        <td>
                            <span class="large-text">
                                <?= $element->name_hp ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?= CHtml::encode($element->getAttributeLabel('second_op')) ?>
                        </td>
                        <td>
                            <span class="highlighter">
                                <?= $element->second_op ? $element->sec_op_hp : 'None'; ?>
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>