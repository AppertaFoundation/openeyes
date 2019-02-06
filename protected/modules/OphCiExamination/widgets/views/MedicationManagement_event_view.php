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
?>
<?php /** @var \OEModule\OphCiExamination\models\MedicationManagement $element */ ?>
<?php $el_id =  CHtml::modelName($element) . '_element'; ?>

    <?php foreach (array("Continued", "Stopped", "Prescribed", "Other") as $section): ?>
        <?php $method = "get{$section}Entries"; ?>
        <?php if (!empty($entries = $element->$method())): ?>
            <div class="element-data">
                <div class="data-value">
                    <div class="tile-data overflow">
                        <table>
                            <colgroup>
                                <col>
                                <col width="55px">
                                <col width="85px">
                            </colgroup>
                            <thead>
                            <tr>
                                <th><?php echo $section; ?></th>
                                <th></th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($entries as $entry): ?>
                                <?php echo $this->render('MedicationManagementEntry_event_view', ['entry' => $entry]); ?>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
