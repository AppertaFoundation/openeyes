<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
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
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>

<div id="<?=$model_name?>_form_wrapper">
    <div class="data-group">
        <div class="cols-2 column">
            <label for="<?=$model_name?>_risk_id">Risk:</label>
        </div>
        <div class="cols-3 column end">
            <?php
            $risks = $this->getRiskOptions();
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

    <div class="data-group hidden" id="<?= $model_name ?>_other_wrapper">
        <div class="cols-2 column">
            <label for="<?=$model_name?>_other_risk">Other Risk:</label>
        </div>
        <div class="cols-3 column end">
            <?=\CHtml::textField($model_name . '_other_risk', '', array('autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete')))?>
        </div>
    </div>

    <div class="data-group">
        <div class="cols-2 column">
            <label for="<?= $model_name ?>_comments">Comments:</label>
        </div>
        <div class="cols-3 column">
            <?=\CHtml::textField($model_name . '_comments', '', array('autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete')))?>
        </div>
        <div class="cols-4 column end">
            <button class="button small primary" id="<?= $model_name ?>_add_entry">Add</button>
        </div>
    </div>
</div>
