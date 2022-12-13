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
<div class="element-fields full-width">
    <?php
    if (!$element->protocol_id) {
        $default_protocol_id = OphTrOperationnote_CXL_Protocol::model()->getDefault();
    } else {
        $default_protocol_id = $element->protocol_id;
    }

    //    echo $form->radioButtons($element, 'protocol_id',  'OphTrOperationnote_CXL_Protocol', $default_protocol_id, false,
    //        false, false, false, array('options' => array('class' => 'protocolChoice')));

    if (!$element->device_id) {
        $default_device_id = OphTrOperationnote_CXL_Device::model()->getDefault();
    } else {
        $default_device_id = $element->device_id;
    }

    if (!$element->epithelial_status_id) {
        $epithelial_status_id = OphTrOperationnote_CXL_Epithelial_Status::model()->getDefault();
    } else {
        $epithelial_status_id = $element->epithelial_status_id;
    }

    if (!$element->epithelial_removal_diameter_id) {
        $default_epithelial_removal_diameter_id = OphTrOperationnote_CXL_Epithelial_Removal_Diameter::model()->getDefault();
    } else {
        $default_epithelial_removal_diameter_id = $element->epithelial_removal_diameter_id;
    }

    if (!$element->interval_between_drops_id) {
        $default_interval_between_drops_id = OphTrOperationnote_CXL_Interval_Between_Drops::model()->getDefault();
    } else {
        $default_interval_between_drops_id = $element->interval_between_drops_id;
    }

    if (!$element->soak_duration_range_id) {
        $default_soak_duration_range_id = OphTrOperationnote_CXL_Soak_Duration::model()->getDefault();
    } else {
        $default_soak_duration_range_id = $element->soak_duration_range_id;
    }

    if (!$element->uv_irradiance_range_id) {
        $default_uv_irradiance_range_id = OphTrOperationnote_CXL_UV_Irradiance_Range::model()->getDefault();
    } else {
        $default_uv_irradiance_range_id = $element->uv_irradiance_range_id;
    }

    if (!$element->total_exposure_time_id) {
        $default_total_exposure_time_id = OphTrOperationnote_CXL_Total_Exposure_Time::model()->getDefault();
    } else {
        $default_total_exposure_time_id = $element->total_exposure_time_id;
    }

    if (!$element->uv_pulse_duration_id) {
        $default_uv_pulse_duration_id = OphTrOperationnote_CXL_UV_Pulse_Duration::model()->getDefault();
    } else {
        $default_uv_pulse_duration_id = $element->uv_pulse_duration_id;
    }

    if (!$element->interpulse_duration_id) {
        $default_interpulse_duration_id = OphTrOperationnote_CXL_Interpulse_Duration::model()->getDefault();
    } else {
        $default_interpulse_duration_id = $element->interpulse_duration_id;
    }

    ?>
    <div class="cols-11 flex-layout flex-top col-gap">
    <div class="cols-7">
    <table class="standard">
        <tbody>
        <tr>
            <td>
                <?= $form->dropDownList(
                    $element,
                    'protocol_id',
                    CHtml::listData(OphTrOperationnote_CXL_Protocol::model()->findAll(), 'id', 'name'),
                    array('options' => array($default_protocol_id => array('selected' => true)), 'prefilled_value' => $template_data['protocol_id'] ?? ''),
                    null,
                    array('field' => 3)
                ); ?>
            </td>
        </tr>

        <tr>
            <td colspan="2">
                <?= $form->radioButtons(
                    $element,
                    'epithelial_removal_method_id',
                    'OphTrOperationnote_CXL_Epithelial_Removal_Method',
                    $element->epithelial_removal_method_id,
                    false,
                    false,
                    false,
                    false,
                    ['class' => 'cols-full', 'prefilled_value' => $template_data['epithelial_removal_method_id'] ?? '']
                ) ?>
            </td>
        </tr>
        <tr>
            <td>
                <?= $form->radioButtons(
                    $element,
                    'mitomycin_c',
                    'OphTrOperationnote_CXL_Mitomycin',
                    $element->mitomycin_c,
                    false,
                    false,
                    false,
                    false,
                    ['class' => 'cols-full', 'prefilled_value' => $template_data['mitomycin_c'] ?? '']
                ); ?>
            </td>
        </tr>
        <tr>
            <td>
                <?= $form->radioButtons(
                    $element,
                    'iontophoresis_id',
                    'OphTrOperationnote_CXL_Iontophoresis',
                    $element->iontophoresis_id,
                    false,
                    false,
                    false,
                    false,
                    ['class' => 'cols-full', 'prefilled_value' => $template_data['iontophoresis_id'] ?? '']
                ); ?>
            </td>
        </tr>
        <tr>
            <td>
                <?= $form->textField(
                    $element,
                    'iontophoresis_current_value',
                    array('size' => 10, 'maxlength' => 10, 'field' => 2, 'data-prefilled-value' => $template_data['iontophoresis_current_value'] ?? ''),
                    false,
                    array('field' => 1)
                ); ?>
            </td>
        </tr>
        <tr>
            <td>
                <?= $form->textField(
                    $element,
                    'iontophoresis_duration_value',
                    array('size' => 10, 'maxlength' => 10, 'field' => 2, 'data-prefilled-value' => $template_data['iontophoresis_duration_value'] ?? ''),
                    false,
                    array('field' => 1)
                ) ?>
            </td>
        </tr>
        <tr>
            <td>
                <?= $form->radioButtons(
                    $element,
                    'riboflavin_preparation_id',
                    'OphTrOperationnote_CXL_Riboflavin_Preparation',
                    $element->riboflavin_preparation_id,
                    false,
                    false,
                    false,
                    false,
                    ['class' => 'cols-full', 'prefilled_value' => $template_data['riboflavin_preparation_id'] ?? '']
                ) ?>
            </td>
        </tr>


        <tr>
            <td colspan="2">
                <?= $form->textArea($element, 'cxl_comments', array(), false, array('rows' => 4, 'cols' => 40, 'data-prefilled-value' => $template_data['cxl_comments'] ?? '')) ?>
            </td>
        </tr>
        </tbody>
    </table>
    </div>

    <div class="cols-4">
        <table class="standard">
            <tbody>
            <tr>
                <td>
                    <?= $form->dropDownList(
                        $element,
                        'device_id',
                        CHtml::listData(OphTrOperationnote_CXL_Device::model()->findAll(), 'id', 'name'),
                        array('options' => array($default_device_id => array('selected' => true))),
                        null,
                        array('field' => 3, 'class' => 'cols-full', 'data-prefilled-value' => $template_data['device_id'] ?? '')
                    ); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?= $form->dropDownList(
                        $element,
                        'epithelial_status_id',
                        CHtml::listData(OphTrOperationnote_CXL_Epithelial_Status::model()->findAll(), 'id', 'name'),
                        array('options' => array($epithelial_status_id => array('selected' => true))),
                        null,
                        array('field' => 3, 'class' => 'cols-full', 'data-prefilled-value' => $template_data['epithelial_status_id'] ?? '')
                    ); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?= $form->dropDownList(
                        $element,
                        'epithelial_removal_diameter_id',
                        CHtml::listData(OphTrOperationnote_CXL_Epithelial_Removal_Diameter::model()->findAll(), 'id', 'name'),
                        array('options' => array($default_epithelial_removal_diameter_id => array('selected' => true))),
                        null,
                        array('field' => 2, 'class' => 'cols-full', 'data-prefilled-value' => $template_data['epithelial_removal_diameter_id'] ?? '')
                    );  ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?= $form->dropDownList(
                        $element,
                        'soak_duration_range_id',
                        CHtml::listData(OphTrOperationnote_CXL_Soak_Duration::model()->findAll(), 'id', 'name'),
                        array('options' => array($default_soak_duration_range_id => array('selected' => true))),
                        null,
                        array('field' => 2, 'class' => 'cols-full', 'data-prefilled-value' => $template_data['soak_duration_range_id'] ?? '')
                    ); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?=  $form->dropDownList(
                        $element,
                        'uv_pulse_duration_id',
                        CHtml::listData(OphTrOperationnote_CXL_UV_Pulse_Duration::model()->findAll(), 'id', 'name'),
                        array('options' => array($default_uv_pulse_duration_id => array('selected' => true))),
                        null,
                        array('field' => 2, 'class' => 'cols-full', 'data-prefilled-value' => $template_data['uv_pulse_duration_id'] ?? '')
                    ); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?= $form->dropDownList(
                        $element,
                        'uv_irradiance_range_id',
                        CHtml::listData(
                            OphTrOperationnote_CXL_UV_Irradiance_Range::model()->findAll(array('order' => 'display_order')),
                            'id',
                            'name'
                        ),
                        array('options' => array($default_uv_irradiance_range_id => array('selected' => true)), 'data-prefilled-value' => $template_data['uv_irradiance_range_id'] ?? ''),
                        null,
                        array('field' => 1)
                    ); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?= $form->textField(
                        $element,
                        'uv_total_energy_value',
                        array('size' => 10, 'maxlength' => 10, 'field' => 2, 'data-prefilled-value' => $template_data['uv_total_energy_value'] ?? ''),
                        false,
                        array('field' => 1)
                    ) ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?= $form->dropDownList(
                        $element,
                        'total_exposure_time_id',
                        CHtml::listData(OphTrOperationnote_CXL_Total_Exposure_Time::model()->findAll(), 'id', 'name'),
                        array('options' => array($default_total_exposure_time_id => array('selected' => true)), 'data-prefilled-value' => $template_data['total_exposure_time_id'] ?? ''),
                        null,
                        array('field' => 1)
                    ); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?= $form->dropDownList(
                        $element,
                        'interpulse_duration_id',
                        CHtml::listData(OphTrOperationnote_CXL_Interpulse_Duration::model()->findAll(), 'id', 'name'),
                        array('options' => array($default_interpulse_duration_id => array('selected' => true)), 'data-prefilled-value' => $template_data['interpulse_duration_id'] ?? ''),
                        null,
                        array('field' => 2)
                    ); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?= $form->radioButtons(
                        $element,
                        'interval_between_drops_id',
                        'OphTrOperationnote_CXL_Interval_Between_Drops',
                        $default_interval_between_drops_id,
                        false,
                        false,
                        false,
                        false,
                        ['class' => 'cols-full', 'prefilled_value' => $template_data['interval_between_drops_id'] ?? '']
                    ) ?>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    </div>


</div>
<script type="text/javascript">
  $('#Element_OphTrOperationnote_CXL_protocol_id').on('change', function () {
    protocolSelection($('#Element_OphTrOperationnote_CXL_protocol_id').val());
  });

  function protocolSelection(chosenProtocol) {
    if (chosenProtocol == 1) {
      var epDiam = "9mm";
      var epDiamSel = $("#Element_OphTrOperationnote_CXL_epithelial_removal_diameter_id option:contains(" + epDiam + ")").attr('selected', 'selected').val();
      $('#Element_OphTrOperationnote_CXL_epithelial_removal_method_id_1').prop("checked", true);
      $('#Element_OphTrOperationnote_CXL_epithelial_removal_diameter_id').val(epDiamSel);
      $('#Element_OphTrOperationnote_CXL_riboflavin_preparation_id_1').prop("checked", true);
      $('#Element_OphTrOperationnote_CXL_interval_between_drops_id_2').prop("checked", true);
      var soakDur = "10 minutes";
      var soakDurSel = $("#Element_OphTrOperationnote_CXL_soak_duration_range_id option:contains(" + soakDur + ")").attr('selected', 'selected').val();
      $('#Element_OphTrOperationnote_CXL_soak_duration_range_id').val(soakDurSel);
      var uvIrr = "30";
      var uvIrrSel = $("#Element_OphTrOperationnote_CXL_uv_irradiance_range_id option:contains(" + uvIrr + ")").attr('selected', 'selected').val();
      $('#Element_OphTrOperationnote_CXL_uv_irradiance_range_id').val(uvIrrSel);
      var totalExp = "4";
      var totalExpSel = $("#Element_OphTrOperationnote_CXL_total_exposure_time_id option:contains(" + totalExp + ")").attr('selected', 'selected').val();
      $('#Element_OphTrOperationnote_CXL_total_exposure_time_id').val(totalExpSel);
    }
    if (chosenProtocol == 2) {
      var epDiam = "9mm";
      var epDiamSel = $("#Element_OphTrOperationnote_CXL_epithelial_removal_diameter_id option:contains(" + epDiam + ")").attr('selected', 'selected').val();
      $('#Element_OphTrOperationnote_CXL_epithelial_removal_method_id_1').prop("checked", true);
      $('#Element_OphTrOperationnote_CXL_epithelial_removal_diameter_id').val(epDiamSel);
      $('#Element_OphTrOperationnote_CXL_riboflavin_preparation_id_1').prop("checked", true);
      $('#Element_OphTrOperationnote_CXL_interval_between_drops_id_2').prop("checked", true);
      var soakDur = "10 minutes";
      var soakDurSel = $("#Element_OphTrOperationnote_CXL_soak_duration_range_id option:contains(" + soakDur + ")").attr('selected', 'selected').val();
      $('#Element_OphTrOperationnote_CXL_soak_duration_range_id').val(soakDurSel);
      var uvIrr = "30";
      var uvIrrSel = $("#Element_OphTrOperationnote_CXL_uv_irradiance_range_id option:contains(" + uvIrr + ")").attr('selected', 'selected').val();
      $('#Element_OphTrOperationnote_CXL_uv_irradiance_range_id').val(uvIrrSel);
      var totalExp = "8";
      var totalExpSel = $("#Element_OphTrOperationnote_CXL_total_exposure_time_id option:contains(" + totalExp + ")").attr('selected', 'selected').val();
      $('#Element_OphTrOperationnote_CXL_total_exposure_time_id').val(totalExpSel);
    }
    if (chosenProtocol == 3) {
      var epDiam = "9mm";
      var epDiamSel = $("#Element_OphTrOperationnote_CXL_epithelial_removal_diameter_id option:contains(" + epDiam + ")").attr('selected', 'selected').val();
      $('#Element_OphTrOperationnote_CXL_epithelial_removal_method_id_1').prop("checked", true);
      $('#Element_OphTrOperationnote_CXL_epithelial_removal_diameter_id').val(epDiamSel);
      $('#Element_OphTrOperationnote_CXL_riboflavin_preparation_id_2').prop("checked", true);
      $('#Element_OphTrOperationnote_CXL_interval_between_drops_id_2').prop("checked", true);
      var soakDur = "30 minutes";
      var soakDurSel = $("#Element_OphTrOperationnote_CXL_soak_duration_range_id option:contains(" + soakDur + ")").attr('selected', 'selected').val();
      $('#Element_OphTrOperationnote_CXL_soak_duration_range_id').val(soakDurSel);
      var uvIrr = "3";
      var uvIrrSel = $("#Element_OphTrOperationnote_CXL_uv_irradiance_range_id option:contains(" + uvIrr + ")").attr('selected', 'selected').val();
      $('#Element_OphTrOperationnote_CXL_uv_irradiance_range_id').val(uvIrrSel);
      var totalExp = "30";
      var totalExpSel = $("#Element_OphTrOperationnote_CXL_total_exposure_time_id option:contains(" + totalExp + ")").attr('selected', 'selected').val();
      $('#Element_OphTrOperationnote_CXL_total_exposure_time_id').val(totalExpSel);
    }
    if (chosenProtocol == 4) {
      $('#Element_OphTrOperationnote_CXL_epithelial_removal_method_id_1').prop("checked", true);
      $('#Element_OphTrOperationnote_CXL_riboflavin_preparation_id_2').prop("checked", true);
      $('#Element_OphTrOperationnote_CXL_interval_between_drops_id_1').prop("checked", true);
      var soakDur = "3 minutes";
      var soakDurSel = $("#Element_OphTrOperationnote_CXL_soak_duration_range_id option:contains(" + soakDur + ")").attr('selected', 'selected').val();
      $('#Element_OphTrOperationnote_CXL_soak_duration_range_id').val(soakDurSel);
      var uvIrr = "30";
      var uvIrrSel = $("#Element_OphTrOperationnote_CXL_uv_irradiance_range_id option:contains(" + uvIrr + ")").attr('selected', 'selected').val();
      $('#Element_OphTrOperationnote_CXL_uv_irradiance_range_id').val(uvIrrSel);
      var totalExp = "1";
      var totalExpSel = $("#Element_OphTrOperationnote_CXL_total_exposure_time_id option:contains(" + totalExp + ")").attr('selected', 'selected').val();
      $('#Element_OphTrOperationnote_CXL_total_exposure_time_id').val(totalExpSel);
    }


  }
</script>