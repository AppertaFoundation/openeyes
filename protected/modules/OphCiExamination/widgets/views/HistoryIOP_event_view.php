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

<div class="element-data element-eyes flex-layout">
    <div class="cols-6 right-eye">
        <table id="<?= $model_name ?>_entry_table" class="cols-10">
            <colgroup>
                <col class="cols-3">
                <col class="cols-3">
                <col class="cols-2">
                <col class="cols-2">
                <col class="cols-2">
            </colgroup>
            <tbody>
            <tr class="divider">
                <td>Goldmann</td>
                <td>11 mmHg</td>
                <td>
                    <input class="fixed-width-small" value="12:15">
                    <?php
                    $this->widget('application.widgets.DatePicker', array(
                        'element' => new \OEModule\OphCiExamination\models\OphCiExamination_IntraocularPressure_Value(),
                        'name' => 'reading_time',
                        'field' => 'reading_time',
                        'options' => array('maxDate' => 'today'),
                        'htmlOptions' => array(
                            'form' => null, // TODO get form
                            'nowrapper' => true,
                            'class' => 'js-iop-date-input'
                        ),
                        'layoutColumns' => array(
                            'label' => 2,
                            'field' => 2,
                        ),
                    ));
                    ?>
                </td>

                <td>
                    <div class="cols-full ">
                        <button class="button  js-add-comments" data-input="block1" style="">
                            <i class="oe-i comments small-icon "></i>
                        </button>
                        <div id="block1" class="cols-full" style="display: none;">
                            <div class=" flex-layout flex-left">
                                <textarea placeholder="Comments" autocomplete="off" rows="1" class="js-input-comments cols-full "
                                    style="overflow-x: hidden; overflow-wrap: break-word;"></textarea>
                                <i class="oe-i remove-circle small-icon pad-left  js-remove-add-comments"></i>
                            </div>
                        </div>
                    </div>
                </td>
                <td><i class="oe-i trash"></i></td>
            </tr>

            <?php foreach ($pastIOPs as $iop) { ?>
                <?php $date = $iop->event->event_date; ?>
                <?php foreach ($iop->right_values as $iop_value) { ?>
                    <tr>
                        <td><?=$iop_value->instrument->name?></td>
                        <td><?=$iop_value->reading->value?>mmHg</td>
                        <td>
                            <i class="oe-i time small no-click pad-right"></i>
                            <?=$iop_value->reading_time?>
                            <span class="oe-date"><?=date('d M Y', strtotime($date));?></span>
                        </td>
                        <td colspan="2">
                            <i class="oe-i comments-added medium js-has-tooltip" data-tooltip-content="Comments shown here...">
                            </i>
                        </td>
                    </tr>
                <?php } ?>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="cols-6 left-eye">
        <table id="<?= $model_name ?>_entry_table" class="cols-10">
            <colgroup>
                <col class="cols-3">
                <col class="cols-3">
                <col class="cols-4">
                <col class="cols-2">
            </colgroup>
            <tbody>
            <tr class="divider">
                <td>Goldmann</td>
                <td>11 mmHg</td>
                <td><input class="fixed-width-small" value="12:15"><input class="datepicker1 date" style="width:90px" placeholder="dd mon yyyy"></td>
                <td>
                    <div class="cols-full ">
                        <button class="button  js-add-comments" data-input="block1" style="">
                            <i class="oe-i comments small-icon "></i>
                        </button>
                        <div id="block1" class="cols-full" style="display: none;">
                            <div class=" flex-layout flex-left">
                                <textarea placeholder="Comments" autocomplete="off" rows="1" class="js-input-comments cols-full "
                                          style="overflow-x: hidden; overflow-wrap: break-word;"></textarea>
                                <i class="oe-i remove-circle small-icon pad-left  js-remove-add-comments"></i>
                            </div>
                        </div>
                    </div>
                </td>
                <td><i class="oe-i trash"></i></td>
            </tr>

            <?php foreach ($pastIOPs as $iop) { ?>
                <?php $date = $iop->event->event_date; ?>
                <?php foreach ($iop->left_values as $iop_value) { ?>
                    <tr>
                        <td><?=$iop_value->instrument->name?></td>
                        <td><?=$iop_value->reading->value?>mmHg</td>
                        <td><?=$iop_value->reading_time?></td>
                        <td><?=date('d M Y', strtotime($date));?></td>
                    </tr>
                <?php } ?>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>


