<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<h2>List of patients seen in A&E</h2>

<div class="row divider">
    <?php
    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'module-report-form',
        'enableAjaxValidation' => false,
        'layoutColumns' => array('label' => 2, 'field' => 10),
        'action' => Yii::app()->createUrl('/' . $this->module->id . '/report/downloadReport'),
    ));
    ?>

  <input type="hidden" name="report-name" value="AE"/>
    <table class="standard cols-full">
        <tbody>
        <tr class="col-gap">
            <td>
                Start date
            </td>
            <td>
                <input name="start_date" type="date" value="<?= date('Y-m-d') ?>"/>
            </td>
        </tr>
        <tr class="col-gap">
            <td>End date</td>
            <td><input name="end_date" type="date" value="<?= date('Y-m-d') ?>"/></td>
        </tr>
        <tr class="col-gap">
            <td>Clinician's name</td>
            <td>
                <?= CHtml::dropDownList('clinician', null, CHtml::listData(User::model()->findAll('is_consultant = 1 OR is_surgeon = 1'), 'id', 'fullname'), ['empty' => '--- Please select ---']) ?>
            </td>
        </tr>
        </tbody>
    </table>
    <?php $this->endWidget(); ?>
  <div class="errors alert-box alert with-icon" style="display: none">
    <p>Please fix the following input errors:</p>
    <ul></ul>
  </div>
  <div class="row flex-layout flex-right">
    <button type="submit" class="button green hint display-module-report" name="run">
      <span class="button-span button-span-blue">Display report</span>
    </button>
    &nbsp;
    <button type="submit" class="button green hint download-module-report" name="run">
      <span class="button-span button-span-blue">Download report</span>
    </button>
    <i class="spinner loader" style="display: none;"></i>
  </div>
  <div class="js-report-summary report-summary" style="display: none;">
  </div>
</div>
