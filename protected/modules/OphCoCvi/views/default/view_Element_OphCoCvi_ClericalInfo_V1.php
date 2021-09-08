<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

?>

<div class="element-data">
    <div class="element-fields row">
        <div class="large-12 column">
            <table>
                <thead>
                <tr>
                    <th>Other relevant factors about the patient</th>
                    <th>Y/N</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $model = OEModule\OphCoCvi\models\Element_OphCoCvi_ClericalInfo_V1::model();
                ?>
                <?php
                foreach ($model->patientFactorList($element->id) as $factor) {
                    $is_factor = $factor['is_factor'];
                    $comments = $factor['comments']; ?>
                    <tr>
                        <td><?php echo CHtml::encode($factor['name']) ?>
                            <?php if ($factor['is_comments'] == 1) { ?>
                                <div class="row data-row"><br/>
                                    <div class="large-4 column large-push-1"
                                         style="font-style: italic;"><?php echo CHtml::encode($factor['label']) ?></div>
                                    <div class="large-6 column large-push-1 end"><?php echo CHtml::encode($comments); ?></div>
                                </div>
                            <?php } ?>
                        </td>
                        <td><?php if (isset($is_factor) && $is_factor == 1) {
                                echo "Y";
                            } elseif (isset($is_factor) && $is_factor == 0) {
                                echo "N";
                            } elseif (isset($is_factor) && $is_factor == 2) {
                                echo "Unknown";
                            } else {
                                echo "None";
                            } ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
   
    <div class="element-fields row">
        <div class="large-12 column">
            <div class="row data-row">
                <div class="large-6 column">
                    <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('preferred_info_fmt_id')) ?></div>
                </div>
                <div class="large-6 column end">
                    <div class="data-value"><?php echo $element->preferred_info_fmt ? CHtml::encode($element->preferred_info_fmt->name) : 'None' ?></div>
                </div>
            </div>
        </div>
        
    </div>
    
    <div class="element-fields row">
        <div class="large-12 column">
            <div class="row data-row">
                <div class="large-6 column">
                    <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('preferred_comm')) ?>:</div>
                </div>
                <div class="large-6 column end">
                    <div class="data-value">
                        <?php echo $element->preferred_comm ? CHtml::encode($element->preferred_comm) : 'None' ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="element-fields row">
        <div class="large-12 column">
            <div class="row data-row">
                <div class="large-6 column">
                    <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('preferred_format_id')) ?></div>
                </div>
                <div class="large-6 column end">
                    <div class="data-value">
                        <?php
                        if ($element->preferred_format_other) {
                            echo CHtml::encode($element->preferred_format_other);
                        }
                        if (!empty($element->preferred_format_assignments)) {
                            echo $element->preferred_format_other ? ', ' : '';
                            $formats = '';
                            foreach ($element->preferred_format_assignments as $preferred_format) {
                                $formats .= $preferred_format->preferred_format->name.', ';
                            }
                            echo CHtml::encode(substr($formats, 0, -2));
                        }
                        if (empty($element->preferred_format_assignments) && !$element->preferred_format_other) {
                            echo 'None';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="element-fields row">
        <div class="large-12 column">
            <div class="row data-row">
                <div class="large-6 column">
                    <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('preferred_language_id')) ?></div>
                </div>
                <div class="large-6 column end">
                    <div class="data-value"><?php
                        echo $element->preferred_language ? CHtml::encode($element->preferred_language->name) : '';
                    if ($element->preferred_language_text) {
                        echo $element->preferred_language ? ', ' : '';
                        echo CHtml::encode($element->preferred_language_text);
                    }
                    if (!$element->preferred_language_text && !$element->preferred_language) {
                        echo 'None';
                    }

                    ?></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="element-fields row">
        <div class="large-12 column">
            <div class="row data-row">
                <div class="large-6 column">
                    <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('interpreter_required')) ?></div>
                </div>
                <div class="large-6 column end">
                    <div class="data-value">
                        <?php echo ($element->interpreter_required == 1) ? "Yes" :'No'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

