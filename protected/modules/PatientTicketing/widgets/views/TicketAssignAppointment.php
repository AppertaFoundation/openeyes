<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
$api = Yii::app()->moduleAPI->get('PatientTicketing');
if ($outcome = $api->getFollowUp($this->ticket->id)) {
    ?>
  <tbody data-formName="<?= $this->form_name ?>">
  <tr>
    <td>Follow-up appointment:</td>
    <td>
      <table class="blank">
        <thead>
        <tr>
          <th>Date</th>
          <th>Time (HH:MM)</th>
        </tr>
        </thead>
        <tbody class="readings-right">
        <tr>
          <td>
              <?php
                $value = @$this->form_data[$this->form_name]['appointment_date'];
                $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                  'name' => $this->form_name . '[appointment_date]',
                  'id' => $this->form_name . '_appointment_date',
                  // additional javascript options for the date picker plugin
                  'options' => array(
                      'showAnim' => 'fold',
                      'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                      'minDate' => 'new Date()',
                  ),
                  'value' => preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)
                      ? Helper::convertMySQL2NHS($value) : $value,
                  'htmlOptions' => ['class'=>'cols-11'],
                )); ?>
          </td>
          <td>
              <?=\CHtml::textField(
                  $this->form_name . '[appointment_time]',
                  @$this->form_data[$this->form_name]['appointment_time'],
                  ['class'=>'cols-11']
              ) ?>
          </td>
        </tr>
        </tbody>
      </table>
    </td>
  </tr>
  </tbody>
<?php } ?>



