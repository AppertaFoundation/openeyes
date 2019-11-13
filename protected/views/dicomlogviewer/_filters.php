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

<div id="dicom-log-filter">
    <table class="standard">
        <colgroup>
            <col class="cols-3">
            <col class="cols-3">
            <col class="cols-3">
            <col class="cols-3">
        </colgroup>
        <thead>
        <tr>
            <td>Station ID</td>
            <td>Location (like)</td>
            <td>Patient Number</td>
            <td>Status</td>
        </tr>
        </thead>

        <tbody>
        <tr>
            <td><?= \CHtml::textField(
                'station_id',
                \Yii::app()->request->getPost('station_id'),
                ['class' => 'small fixed-width cols-11', 'placeholder' => 'Station Id']
            ); ?>
            </td>
            <td><?= \CHtml::textField(
                'location',
                \Yii::app()->request->getPost('location'),
                ['class' => 'small fixed-width cols-11', 'placeholder' => 'Location']
            ); ?></td>
            <td><?= \CHtml::textField(
                'hos_num',
                \Yii::app()->request->getPost('hos_num'),
                ['class' => 'small fixed-width cols-11', 'placeholder' => 'Hospital Number']
            ); ?></td>
            <td><?= \CHtml::dropDownList(
                'status',
                'status',
                ['' => 'All', 'success' => 'Success', 'failed' => 'Failed'],
                ['class' => 'cols-11']
            ); ?></td>
        </tr>
        </tbody>
    </table>

    <table class="standard">
        <colgroup>
            <col class="cols-2">
            <col class="cols-5">
            <col class="cols-5">
        </colgroup>
        <thead>
        <tr>
            <td>Type</td>
            <td>Study Instance ID (like)</td>
            <td>File name (like)</td>
        </tr>
        </thead>

        <tbody>
        <tr>
            <td><?= CHtml::dropDownList(
                'type',
                'type',
                ['' => 'All types', 'biometry' => 'Biometry'],
                ['class' => 'cols-full']
                ); ?></td>
            <td>
                <?php $this->widget('application.widgets.AutoCompleteSearch', ['field_name' => 'study_id']); ?>
            </td>
            <td>
                <?php $this->widget('application.widgets.AutoCompleteSearch', ['field_name' => 'file_name']); ?>
            </td>
        </tr>
        </tbody>
    </table>

    <table class="standard">
        <tbody>
        <tr>
            <td>
                <?=\CHtml::radioButtonList(
                    'date_type',
                    2,
                    ['1' => 'Import date', '2' => 'Study date'],
                    ['separator' => '  ']
                ); ?>
                <label class="inline" for="date_from">From:</label>
                <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                    'name' => 'date_from',
                    'id' => 'date_from',
                    'options' => array(
                        'showAnim' => 'fold',
                        'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                    ),
                    'value' => \Yii::app()->request->getPost('date_from'),
                    'htmlOptions' => array(
                        'class' => 'small fixed-width',
                    ),
                )) ?>
                <label class="inline" for="date_to">To:</label>
                <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                    'name' => 'date_to',
                    'id' => 'date_to',
                    'options' => array(
                        'showAnim' => 'fold',
                        'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                    ),
                    'value' => \Yii::app()->request->getPost('date_to'),
                    'htmlOptions' => array(
                        'class' => 'small fixed-width',
                    ),
                )) ?>
            </td>
        </tr>
        </tbody>

        <tfoot class="pagination-container">
        <tr>
            <td colspan="3">
                <button type="button" id="dicom-log-search" class="secondary large">Search</button>
            </td>
        </tr>
        </tfoot>
    </table>
</div>
<script>
    OpenEyes.UI.AutoCompleteSearch.init({
        input: $('#study_id'),
        url: '/audit/users',
        onSelect: function(){
            let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
            $('#study_id').val(AutoCompleteResponse);
        }
    });

    OpenEyes.UI.AutoCompleteSearch.init({
        input: $('#file_name'),
        url: '/audit/users',
        onSelect: function(){
            let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
            $('#file_name').val(AutoCompleteResponse);
        }
    });
</script>
