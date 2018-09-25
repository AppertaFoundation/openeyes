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


    <table class="cols-full" id="dicom-log-filter">
        <colgroup>
            <col class="cols-3">
            <col class="cols-3">
            <col class="cols-3">
            <col class="cols-3">
        </colgroup>
        <tbody>
        <tr>
            <td><?php echo CHtml::textField('station_id', /*@$_POST['station_id']*/'IOLM1120642', [
                    'class' => 'small fixed-width', 'placeholder' => 'Station Id']); ?>
            </td>
            <td><?php echo CHtml::textField('location', @$_POST['location'],
                    ['class' => 'small fixed-width', 'placeholder' => 'Location']); ?></td>
            <td><?php echo CHtml::textField('hos_num', @$_POST['hos_num'],
                    ['class' => 'small fixed-width', 'placeholder' => 'Hospital Number']); ?></td>
            <td><?php echo CHtml::dropDownList('status', 'status',
                    ['' => 'All Status', 'success' => 'Success', 'failed' => 'Failed']); ?></td>
        </tr>
        <tr class="col-gap">
            <td><?= CHtml::dropDownList('type', 'type', ['' => 'All types', 'biometry' => 'Biometry']); ?></td>
            <td><?php
                $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                    'id' => 'study_id',
                    'name' => 'study_id',
                    'value' => '',
                    'sourceUrl' => array('audit/users'),
                    'options' => array(
                        'minLength' => '3',
                    ),
                    'htmlOptions' => array(
                        'placeholder' => 'Type to search for Study Instance ID...',
                    ),
                ));
                ?>
            </td>
            <td>
                <?php
                $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                    'id' => 'file_name',
                    'name' => 'file_name',
                    'value' => '',
                    'sourceUrl' => array('audit/users'),
                    'options' => array(
                        'minLength' => '3',
                    ),
                    'htmlOptions' => array(
                        'placeholder' => 'Type to search for File name...',
                    ),
                ));
                ?>
            </td>
        </tr>
        <tr>
            <td>
                <label class="inline" for="import_date">Import date:</label>
                <?php
                echo CHtml::radioButton('date_type', true, array(
                    'value' => '1',
                    'id' => 'import_date',
                    'name' => 'date',
                    'uncheckValue' => null,
                )); ?>
            </td>
            <td>
                <label class="inline" for="study_date">Study date:</label>
                <?php
                echo CHtml::radioButton('study_date', false, array(
                    'value' => '2',
                    'id' => 'study_date',
                    'name' => 'date',
                    'uncheckValue' => null,
                ));
                ?></span>&nbsp;
            </td>
            <td>
                <label class="inline" for="date_from">From:</label>
                <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                    'name' => 'date_from',
                    'id' => 'date_from',
                    'options' => array(
                        'showAnim' => 'fold',
                        'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                    ),
                    'value' => @$_POST['date_from'],
                    'htmlOptions' => array(
                        'class' => 'small fixed-width',
                    ),
                )) ?>
            </td>
            <td>
                <label class="inline" for="date_to">To:</label>
                <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                    'name' => 'date_to',
                    'id' => 'date_to',
                    'options' => array(
                        'showAnim' => 'fold',
                        'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                    ),
                    'value' => @$_POST['date_to'],
                    'htmlOptions' => array(
                        'class' => 'small fixed-width',
                    ),
                )) ?>
            </td>
        </tr>
        </tbody>
        <tfoot class="pagination-container">
        <tr>
            <td>
                <button type="button" id="dicom-log-search" class="secondary large">Search</button>
            </td>
        </tr>
        </tfoot>
    </table>
