<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 * version. OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details. You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled
 * COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div id="<?=$model_name?>_form_wrapper">
    <div class="field-row row">
        <div class="large-2 column">
            <label for="<?=$model_name?>_risk_id">Risk:</label>
        </div>
        <div class="large-3 column end">
            <?php
            $risks = $element->getRiskOptions();
            $risks_opts = array(
                'options' => array(),
                'empty' => '- select -',
            );
            foreach ($risks as $risk) {
                $risks_opts['options'][$risk->id] = array('data-other' => $risk->isOther() ? '1' : '0');
            }
            echo CHtml::dropDownList($model_name . '_risk_id', '', CHtml::listData($risks, 'id', 'name'), $risks_opts)
            ?>
        </div>
    </div>

    <div class="field-row row hidden" id="<?= $model_name ?>_other_wrapper">
        <div class="large-2 column">
            <label for="<?=$model_name?>_other_risk">Other Risk:</label>
        </div>
        <div class="large-3 column end">
            <?php echo CHtml::textField($model_name . '_other_risk', '', array('autocomplete' => Yii::app()->params['html_autocomplete']))?>
        </div>
    </div>

    <div class="field-row row">
        <div class="large-2 column">
            <label for="<?= $model_name ?>_comments">Comments:</label>
        </div>
        <div class="large-3 column">
            <?php echo CHtml::textField($model_name . '_comments', '', array('autocomplete' => Yii::app()->params['html_autocomplete']))?>
        </div>
        <div class="large-4 column end">
            <button class="button small primary" id="<?= $model_name ?>_add_entry">Add</button>
        </div>
    </div>
</div>