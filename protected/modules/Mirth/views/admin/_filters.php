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

<div id="mirth-log-filter">
    <table class="standard">
        <colgroup>
        <col class="cols-3">
            <col class="cols-1">
            <col class="cols-3">
            <col class="cols-2">
            <col class="cols-2">
            <col class="cols-2">
        </colgroup>
        <thead>
            <tr>
                <td>Channel</td>
                <td>Filter</td>
                <td>Hospital Number</td>
                <td>Date</td>
                <td></td>
                <td></td>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td><?= \CHtml::dropDownList(
                    'channel',
                    'channel',
                    \CHtml::listData(
                        $channels,
                        'ID',
                        'NAME'
                    ),
                    ['empty' => '- Select -','class' => 'cols-11']
                ); ?></td>
                <td>
                    <input type="hidden" name="filter" value="0"/>
                        <?=\CHtml::checkBox('filter'); ?>
                        <?=\CHtml::label('Error Only', 'filter') ?>
                </td>
                <td><?= \CHtml::textField(
                    'hos_num',
                    \Yii::app()->request->getPost('hos_num'),
                    ['class' => 'small fixed-width cols-11', 'placeholder' => 'Hospital Number']
                ); ?></td>
                <td>
                    <label class="inline" for="dateFrom">From:</label>
                    <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        'name' => 'dateFrom',
                        'id' => 'dateFrom',
                        'options' => array(
                            'showAnim' => 'fold',
                            'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                        ),
                        'value' => \Yii::app()->request->getPost('dateFrom'),
                        'htmlOptions' => array(
                            'class' => 'small fixed-width',
                        ),
                    )) ?>
                </td>
                <td>
                    <label class="inline" for="dateTo">To:</label>
                    <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        'name' => 'dateTo',
                        'id' => 'dateTo',
                        'options' => array(
                            'showAnim' => 'fold',
                            'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                        ),
                        'value' => \Yii::app()->request->getPost('dateTo'),
                        'htmlOptions' => array(
                            'class' => 'small fixed-width',
                        ),
                    )) ?>
                </td>
                <td colspan="3">
                    <button type="button" id="mirth-log-search" class="secondary large">Search</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>
