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
$pastIOPs = $this->getPastIOPs();
?>

<?php if ($this->element):
    echo \CHtml::activeHiddenField($this->element, "id");
endif; ?>

<div class="element-fields flex-layout full-width" id="<?= $model_name ?>_element">
    <table id="<?= $model_name ?>_entry_table" class="cols-10">
        <colgroup>
            <col class="cols-3">
            <col class="cols-3">
            <col class="cols-4">
            <col class="cols-2">
        </colgroup>
        <tbody>

        <?php
            foreach ($pastIOPs as $IOP) { ?>
                <tr>
                    <td><?= $IOP->id ?></td>
                    <td><?= $IOP->event_id ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>

    <div class="add-data-actions flex-item-bottom" id="history-allergy-popup">
        <button class="button hint green js-add-select-search" id="add-allergy-btn" type="button"><i
                    class="oe-i plus pro-theme"></i></button>
    </div>
</div>