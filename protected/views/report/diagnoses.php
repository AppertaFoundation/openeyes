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
<h2>Diagnoses report</h2>

<div class="row divider">
    <?php
    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'report-form',
        'enableAjaxValidation' => false,
        'layoutColumns' => array('label' => 2, 'field' => 10),
        'action' => Yii::app()->createUrl('/report/downloadReport'),
    )) ?>

    <input type="hidden" name="report-name" value="Diagnoses"/>

    <table class="standard cols-full">
        <colgroup>
            <col class="cols-3">
            <col class="cols-3">
            <col class="cols-3">
            <col class="cols-3">
        </colgroup>
        <tbody>
        <tr class="col-gap">
            <td>Start date:</td>
            <td>
                <input id="start_date"
                       class="start-date"
                       placeholder="dd-mm-yyyy"
                       name="start_date"
                       autocomplete="off"
                       value= <?= date('d-m-Y'); ?>
                >
            </td>
            <td>End date:</td>
            <td>
                <input id="end_date"
                       class="end-date"
                       placeholder="dd-mm-yyyy"
                       name="end_date"
                       autocomplete="off"
                       value= <?= date('d-m-Y'); ?>
                >
            </td>
        </tr>
        <?php $this->renderPartial('_institution_table_row', ['field_name' => "institution_id"]);?>
        </tbody>
    </table>

    <table>
        <tbody>
        <tr>
            <td>
                <div id="selected_diagnoses">
                </div>
                <?php $this->widget('application.widgets.DiagnosisSelection', array(
                    'field' => 'disorder_id',
                    'options' => CommonOphthalmicDisorder::getList(Firm::model()->findByPk($this->selectedFirmId)),
                    'layout' => 'minimal',
                    'callback' => 'Reports_AddDiagnosis',
                )) ?>
            </td>
        </tr>
        </tbody>
    </table>
    <table class="standard cols-full">
        <colgroup>
            <col class="cols-4">
            <col class="cols-4">
            <col class="cols-4">
        </colgroup>
        <thead>
        <tr>
            <th>Diagnosis</th>
            <th>Principal</th>
            <th>Edit</th>
        </tr>
        </thead>
        <tbody id="Reports_diagnoses">
        </tbody>
    </table>

    <table class="standard cols-full">
        <tbody>
        <tr>
            <td>
                <input type="radio" name="condition_type" id="condition_or" value="or" checked="checked"/>
            </td>
            <td>
                Match patients with <strong>any</strong> of these diagnoses
            </td>
        </tr>
        <tr>
            <td>
                <input type="radio" name="condition_type" id="condition_and" value="and"/>
            </td>
            <td>
                Match patients with <strong>all</strong> of these diagnoses
            </td>
        </tr>
        </tbody>
    </table>

    <?php $this->endWidget() ?>

    <div class="errors alert-box alert with-icon" style="display: none">
        <p>Please fix the following input errors:</p>
        <ul>
        </ul>
    </div>

    <table class="standard cols-full">
        <tbody>
        <tr>
            <td>
                <div class="row flex-layout flex-right">
                    <button type="submit" class="button green hint display-report" name="run">
                        <span class="button-span button-span-blue">Display report</span>
                    </button>
                    &nbsp;
                    <button type="submit" class="button green hint download-report" name="run">
                        <span class="button-span button-span-blue">Download report</span>
                    </button>
                    <i class="spinner loader" style="display: none;"></i>
                </div>
            </td>
        </tr>
        </tbody>
    </table>

    <div class="js-report-summary report-summary" style="display: none;">
    </div>
</div>
