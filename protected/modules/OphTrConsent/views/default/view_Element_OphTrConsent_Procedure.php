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
<div class="element-data full-width">
    <div class="flex-t">
        <div class="cols-5">
            <table class="cols-full large-text">
                <tbody>
                    <?php foreach ($element->procedure_assignments as $assigned_proc) { ?>
                        <tr>
                            <td>
                                <span class="oe-eye-lat-icons">
                                    <i class="oe-i laterality <?= $element->getLateralityIcon()[$assigned_proc->eye_id]['right'] ?> small pad"></i>
                                    <i class="oe-i laterality <?= $element->getLateralityIcon()[$assigned_proc->eye_id]['left'] ?> small pad"></i>
                                </span>
                            </td>
                            <td><?= $assigned_proc->proc->term ?></td>
                            <td>
                                <?php if ($element->booking_event_id) { ?>
                                    <i class="oe-i-e i-TrOperation js-has-tooltip" data-tooltip-content="Procedure info from Op Booking"></i>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php foreach ($element->additionalprocedure_assignments as $additional_proc) { ?>
                        <tr>
                            <td>
                                <span class="oe-eye-lat-icons">
                                    <i class="oe-i laterality <?= $element->getLateralityIcon()[$additional_proc->eye_id]['right'] ?> small pad"></i>
                                    <i class="oe-i laterality <?= $element->getLateralityIcon()[$additional_proc->eye_id]['left'] ?> small pad"></i>
                                </span>
                            </td>
                            <td><?= $additional_proc->proc->term ?></td>
                            <td>
                                Legacy Additional Procedure
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <th>Anaesthetic</th>
                        <td><?= implode(' + ', $element->anaesthetic_type) ?></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="cols-5">
            <table class="last-left">
                <thead>
                    <tr>
                        <th>Extra procedures which may become necessary during the procedure(s)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $extra_proc = $element->event->getElementByClass("Element_OphTrConsent_ExtraProcedures");
                    if (!empty($extra_proc)) {
                        echo $extra_proc->extraProceduresView();
                    } else {
                        echo '<tr><td>None</td></tr>';
                    }?>
                </tbody>
            </table>
        </div>
    </div>
</div>