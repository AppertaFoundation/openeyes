<?php
/**
 * (C) OpenEyes Foundation, 2018
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
<div id="div_LetterString_name" class="data-group">
    <div class="cols-2 column">
        <label for="LetterString_name">Body:</label>
    </div>
    <div class="cols-5 column end">
        <?php echo  CHtml::activeTextArea($model, 'body', ['class' => 'cols-full autosize'])?>
    </div>
</div>
    <div class="cols-8 large-offset-2 column">
        <div class="data-group">
            <div class="cols-3 column">
                <label for="shortcode">
                    Add shortcode:
                </label>
            </div>
            <div class="cols-6 column end">
                <?=\CHtml::dropDownList('shortcode', '', CHtml::listData(PatientShortcode::model()->findAll(array('order' => 'description asc')), 'code', 'description'), array('empty' => '- Select -'))?>
            </div>
        </div>
    </div>

