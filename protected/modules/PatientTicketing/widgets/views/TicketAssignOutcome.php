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
?>
<tbody data-formName="<?= $this->form_name ?>">
<tr>
    <td><?=$this->label;?>:</td>
  <td>
        <?php $outcomes = $this->getOutcomeOptions();
        echo CHtml::dropDownList(
            $this->form_name . '[outcome]',
            @$this->form_data[$this->form_name]['outcome'],
            $outcomes['list_data'],
            array('empty' => 'Select', 'options' => $outcomes['options'], 'class' => 'outcome-select cols-full')
        ); ?>
  </td>
</tr>
</tbody>
<tbody
    id="<?= $this->form_name ?>-followup"
    style="<?php if ($this->hideFollowUp && !@$this->form_data[$this->form_name]['followup_quantity']) {
        ?>display: none;<?php
           } ?>"
>
<tr>
  <td>Follow up:</td>
  <td>
        <?php
        $html_options = array('empty' => 'Select', 'options' => array(), 'class' => 'inline');
        echo CHtml::dropDownList(
            $this->form_name . '[followup_quantity]',
            @$this->form_data[$this->form_name]['followup_quantity'],
            Yii::app()->params['follow_up_months'],
            $html_options
        );
        echo CHtml::dropDownList(
            $this->form_name . '[followup_period]',
            @$this->form_data[$this->form_name]['followup_period'],
            CHtml::listData(\Period::model()->findAll(array('order' => 'display_order')), 'name', 'name'),
            $html_options
        );
        ?>
  </td>
</tr>
<tr>
  <td>Clinic location:</td>
  <td>
        <?=\CHtml::dropDownList(
            $this->form_name . '[clinic_location]',
            @$this->form_data[$this->form_name]['clinic_location'],
            \CHtml::listData(
                $this->getClinicLocations(),
                'name',
                'name'
            ),
            ['empty' => 'Select', 'options' => array(), 'class' => 'cols-full']
        ); ?>
  </td>
</tr>
</tbody>
<script>
    $(document).ready(function() {
        $(this).on('change', '.outcome-select', function() {
            var fup = $(this).find('option:selected').data('followup');
            var formName = $(this).parents('tbody').data('formname');
            if (fup) {
                $('#'+formName+'-followup').show();
            }
            else {
                $('#'+formName+'-followup').hide();
            }
        });
    });
</script>