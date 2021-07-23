<?php
/**
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
$fpten_setting = SettingMetadata::model()->getSetting('prescription_form_format');
$fpten_dispense_condition_id = OphDrPrescription_DispenseCondition::model()->findByAttributes(array('name' => 'Print to {form_type}'))->id;
?>
<h2>Prescribed drugs report</h2>

<div class="row divider">
    <?php $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
          'id' => 'report-form',
          'enableAjaxValidation' => false,
          'layoutColumns' => array('label' => 2, 'field' => 10),
          'action' => Yii::app()->createUrl('/OphDrPrescription/report/downloadReport'),
      )) ?>
    <input type="hidden" name="report-name" value="PrescribedDrugs"/>

  <table class="standard cols-full">
    <colgroup>
      <col class="cols-2">
    </colgroup>
    <tbody>
    <tr>
      <td>Drugs:</td>
      <td>
            <?php
          // set name to null as it is not required to send this value to the server
            echo CHtml::dropDownList(
                null,
                null,
                CHtml::listData($drugs, 'id', 'preferred_term'),
                array('empty' => '-- Select --', 'id' => 'drug_id')
            );
            ?>
          <div class="cols-4">
            <?php $this->widget('application.widgets.AutoCompleteSearch'); ?>
          </div>
      </td>
      <td>
        <img class="autocomplete-loader" style="display: none;"
             src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif') ?>" alt="loading...">
      </td>
    </tr>
    </tbody>
  </table>
  <table class="standard cols-full">
    <colgroup>
      <col class="cols-2">
      <col class="cols-4">
    </colgroup>
    <tbody>
    <?php $this->renderPartial('//report/_institution_table_row', ['field_name' => "institution_id"]);?>
    <tr>
      <td>Date from:</td>
      <td>
        <input id="OphDrPrescription_ReportPrescribedDrugs_start_date"
               placeholder="From"
               class="start-date"
               name="OphDrPrescription_ReportPrescribedDrugs[start_date]"
               autocomplete="off"
               value= <?= date('d-m-Y'); ?>
        >
      </td>
      <td>Date to:</td>
      <td>
        <input id="OphDrPrescription_ReportPrescribedDrugs_end_date"
               placeholder="To"
               class="end-date"
               name="OphDrPrescription_ReportPrescribedDrugs[end_date]"
               autocomplete="off"
               value= <?= date('d-m-Y'); ?>
        >
      </td>
    </tr>
    <tr>
      <td>User</td>
      <td>
            <?php if (Yii::app()->getAuthManager()->checkAccess('Report', Yii::app()->user->id)) : ?>
                <?=\CHtml::dropDownList(
                    'OphDrPrescription_ReportPrescribedDrugs[user_id]',
                    '',
                    CHtml::listData($users, 'id', 'fullName'),
                    array('empty' => 'Select')
                ) ?>
            <?php else : ?>
                <?php
                $user = User::model()->findByPk(Yii::app()->user->id);
                echo CHtml::dropDownList(
                    null,
                    '',
                    array(Yii::app()->user->id => $user->fullName),
                    array(
                      'disabled' => 'disabled',
                      'readonly' => 'readonly',
                      'style' => 'background-color:#D3D3D3;',
                    ) //for some reason the chrome doesn't gray out
                );
                echo CHtml::hiddenField('OphDrPrescription_ReportPrescribedDrugs[user_id]', Yii::app()->user->id);
                ?>
            <?php endif ?>
      </td>
    </tr>
    <tr>
      <td>Dispense Condition/Location</td>
      <td>
        <?= CHtml::dropDownList('OphDrPrescription_ReportPrescribedDrugs[dispense_condition]', '', CHtml::listData($dispense_conditions, 'id', 'name'), array('empty' => 'Select', 'options' => array(
            $fpten_dispense_condition_id => array('label' => "Print to $fpten_setting")
            )))?>
      </td>
      <td>Report Type</td>
      <td>
        <input id="report_type_non_pgd" type="radio" name="OphDrPrescription_ReportPrescribedDrugs[report_type]" value="0">
        <label for="report_type_non_pgd">Non-PGD</label>
        <input id="report_type_pgd" type="radio" name="OphDrPrescription_ReportPrescribedDrugs[report_type]" value="1"> 
        <label for="report_type_pgd">PGD Only</label>
        <input id="report_type_all" type="radio" name="OphDrPrescription_ReportPrescribedDrugs[report_type]" value="2" checked>
        <label for="report_type_all">All</label>
      </td>
    </tr>
    </tbody>
  </table>
  <table class="standard cols-6" id="report-drug-list">
    <colgroup>
      <col class="cols-6">
    </colgroup>
    <thead>
    <tr>
      <th>Drug name</th>
      <th>Action</th>
    </tr>
    </thead>
    <tbody>
    <tr class="no-drugs">
      <td>No drugs selected</td>
    </tr>
    </tbody>
  </table>

    <?php $this->endWidget() ?>

  <div class="errors alert-box alert with-icon" style="display: none">
    <p>Please fix the following input errors:</p>
    <ul></ul>
  </div>

  <table class="standard cols-full">
    <tbody>
    <tr>
      <td>
        <div class="row flex-layout flex-right">
          <button type="submit" class="button green hint display-report" name="run"><span
                class="button-span button-span-blue">Display report</span></button>
          &nbsp;
          <button type="submit" class="button green hint download-report" name="run"><span
                class="button-span button-span-blue">Download report</span></button>
          <i class="spinner loader" style="display: none;"></i>
        </div>
      </td>
    </tr>
    </tbody>
  </table>


  <div class="js-report-summary report-summary" style="display: none; overflow: auto">
  </div>
</div>
<script>
  OpenEyes.UI.AutoCompleteSearch.init({
    input: $('#oe-autocompletesearch'),
    url: '/OphDrPrescription/default/DrugList',
    onSelect: function(){
      let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
      var tr = $('#report-drug-list').find('tr#' + AutoCompleteResponse.id);
      if ( tr.length === 0 ){
        $('.no-drugs').hide();
        addItem(AutoCompleteResponse);
      }
    }
  });
</script>