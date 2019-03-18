<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 *  You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<div class="operation-meta">
    <div class="data-group flex-layout">
        <div class="cols-3">
            <div class="data-label">Operation(s) Performed:</div>
        </div>
        <div class="cols-9">
            <ul>
                <?php
                $operations_perf = Element_OphTrOperationnote_ProcedureList::model()->find('event_id = ?', array($this->event->id));
                foreach ($operations_perf->procedures as $procedure) {
                    echo "<li>{$operations_perf->eye->name} {$procedure->term}</li>";
                }
                ?>
            </ul>
        </div>
    </div>
    <?php
    $surgeon_element = Element_OphTrOperationnote_Surgeon::model()->find('event_id = ?', array($this->event->id));
    $surgeon_name = ($surgeon = User::model()->findByPk($surgeon_element->surgeon_id)) ? $surgeon->getFullNameAndTitle() : 'Unknown';
    $assistant_name = ($assistant = User::model()->findByPk($surgeon_element->assistant_id)) ? $assistant->getFullNameAndTitle() : 'None';
    $supervising_surg_name = ($supervising_surg = User::model()->findByPk($surgeon_element->supervising_surgeon_id)) ? $supervising_surg->getFullNameAndTitle() : 'None';
    ?>
    <table>
        <tbody>
            <tr>
                <th class="cols-4">First Surgeon</th>
                <th class="cols-4">Assistant Surgeon</th>
                <th class="cols-4">Supervising surgeon</th>
            </tr>
            <tr>
                <td><?php echo $surgeon_name ?></td>
                <td><?php echo $assistant_name ?></td>
                <td><?php echo $supervising_surg_name ?></td>
            </tr>
        </tbody>
    </table>

