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

<?php

$model_name = CHtml::modelName($element);
$element_errors = $element->getErrors();
?>
<script type="text/javascript" src="<?= $this->getJsPublishedPath('HistoryRisks.js') ?>"></script>
<script type="text/javascript" src="<?= $this->getJsPublishedPath('HistoryMedications.js') ?>"></script>
<div class="element-fields full-width" id="<?= $model_name ?>_element">
    <div class="data-group flex-layout cols-10">
        <input type="hidden" name="<?= $model_name ?>[present]" value="1" />
        <table id="<?= $model_name ?>_entry_table" class=" cols-full <?php echo $element_errors ? 'highlighted-error error' : '' ?>">
            <colgroup>
                <col class="cols-2">
                <col class="cols-4">
                <col>
                <col>
                <col class="cols-1">
            </colgroup>
            <thead>
            <tr>
                <th></th>
                <th>Start</th>
                <th>Stopped(Optional)</th>
                <th>Reason</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($this->contacts as $contact) { ?>
                <tr>
                <td><?= $contact->contact->fullName; ?></td>
                <td><?= $contact->getLocationText(); ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="flex-layout flex-right">
        <div class="add-data-actions flex-item-bottom" id="medication-history-popup">
            <button class="button hint green js-add-select-search" id="add-medication-btn" type="button">
                <i class="oe-i plus pro-theme"></i>
            </button>
        </div>
    </div>