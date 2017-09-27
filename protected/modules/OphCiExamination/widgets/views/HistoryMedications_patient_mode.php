<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
$model_name = CHtml::modelName($element);
?>

<?php if (!$current && !$stopped) { ?>
    <p>No medications recorded.</p>
<?php } else { ?>
    <table class="plain valign-top patient-data" id="<?= $model_name ?>_entry_table">
        <tbody>
        <?php if ($current) { ?>
            <tr>
                <th colspan="3">Current Medications</th>
            </tr>
            <?php
            foreach ($current as $entry) { ?>
                <tr>
                    <td><?= $entry->getMedicationDisplay() ?></td>
                    <td><?= $entry->getDatesDisplay() ?></td>
                    <td><?php if ($entry->prescription_item) { ?>
                            <a href="<?= $this->getPrescriptionLink($entry) ?>"><span class="has-tooltip fa fa-eye"
                                                                                      data-tooltip-content="View prescription"></span></a>
                        <?php } ?></td>
                </tr>
            <?php }
        }
        if ($stopped) { ?>
            <tr>
                <th colspan="3">Stopped Medications</th>
            </tr>
            <?php
            foreach ($stopped as $entry) { ?>
                <tr>
                    <td><?= $entry->getMedicationDisplay() ?></td>
                    <td><?= $entry->getDatesDisplay() ?></td>
                    <td><?php if ($entry->prescription_item) { ?>
                            <a href="<?= $this->getPrescriptionLink($entry) ?>"><span class="has-tooltip fa fa-eye"
                                                                                      data-tooltip-content="View prescription"></span></a>
                        <?php } ?></td>
                </tr>
            <?php }
        } ?>
        </tbody>
    </table>
<?php } ?>