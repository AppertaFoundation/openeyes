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
?>

<div class="element-data full-width">
    <table class="last-left">
        <colgroup>
            <col class="cols-3"/>
            <col class="cols-8"/>
        </colgroup>
        <tbody>
        <tr>
            <td>
                <span class="data-label fade"><?= $element->getAttributeLabel('nrf_check') ?></span>
            </td>
            <td>
                <span class="data-value large-text">
                    <?php switch ($element->nrf_check) {
                        case 1:
                            echo 'Confirmed';
                            break;
                        case 0:
                            echo 'No';
                            break;
                        default:
                            echo 'Unconfirmed';
                            break;
                    } ?>
                </span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="data-label fade">Red flags</span>
            </td>
            <td>
                <ul class="dot-list">
                    <?php foreach ($element->flag_assignment as $temp_flag) :?>
                        <li><?= $temp_flag->flag->name ?></li>
                    <?php endforeach; ?>
                </ul>
                <span class="data-value large-text"></span>
            </td>
        </tr>
        </tbody>
    </table>
</div>