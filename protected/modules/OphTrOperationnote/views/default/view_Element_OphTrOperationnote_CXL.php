<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>

<section class="element">

    <header class="sub-element-header">
        <h3 class="element-title highlight"><?php echo $element->elementType->name ?></h3>
    </header>
    <div class="element-data">
        <div class="row">
            <div class="large-3 column">
                <h4 class="data-title">
                    <?php echo CHtml::encode($element->getAttributeLabel('protocol_id'))?>:
                </h4>
                <div class="data-value">
                    <?php
                    $protocolName = OphTrOperationnote_CXL_Protocol::model()->find('id=?', $element->protocol_id);
                    echo $protocolName->name;
                    ?>
                </div>
            </div>
            <div class="large-3 column">
                <h4 class="data-title">
                    <?php echo CHtml::encode($element->getAttributeLabel('device_id'))?>:
                </h4>
                <div class="data-value">
                    <?php
                    $deviceName = OphTrOperationnote_CXL_Device::model()->find('id=?', $element->device_id);
                    echo $deviceName->name;
                    ?>
                </div>
            </div>
            <div class="large-3 column">
                <h4 class="data-title">
                    <?php echo CHtml::encode($element->getAttributeLabel('epithelial_removal_method_id'))?>:
                </h4>
                <div class="data-value">
                    <?php
                    if($element->epithelial_removal_method_id){
                    $epiremmetName = OphTrOperationnote_CXL_Epithelial_Removal_Method::model()->find('id=?', $element->epithelial_removal_method_id);
                    echo $epiremmetName->name;
                    }
                    ?>
                </div>
            </div>
            <div class="large-3 column">
                <h4 class="data-title">
                    <?php echo CHtml::encode($element->getAttributeLabel('epithelial_removal_diameter_id'))?>:
                </h4>
                <div class="data-value">
                    <?php
                    $epithelial_removal_diameter_id = OphTrOperationnote_CXL_Epithelial_Removal_Diameter::model()->find('id=?', $element->epithelial_removal_diameter_id);
                    echo $epithelial_removal_diameter_id->name;
                    ?>
                </div>
            </div>
            <div class="large-3 column">
                <h4 class="data-title">
                    <?php echo CHtml::encode($element->getAttributeLabel('iontophoresis_id'))?>:
                </h4>
                <div class="data-value">
                    <?php
                    if($element->iontophoresis_id){
                    $iontophoresis_id = OphTrOperationnote_CXL_Iontophoresis::model()->find('id=?', $element->iontophoresis_id);
                    echo $iontophoresis_id->name;
                    }
                    ?>
                </div>
            </div>
            <div class="large-3 column">
                <h4 class="data-title">
                    <?php echo CHtml::encode($element->getAttributeLabel('iontophoresis_current_value'))?>:
                </h4>
                <div class="data-value">
                    <?php
                    if($element->iontophoresis_current_value){
                    echo $element->iontophoresis_current_value . ' mA';
                    }
                    ?>
                </div>
            </div>
            <div class="large-3 column">
                <h4 class="data-title">
                    <?php echo CHtml::encode($element->getAttributeLabel('iontophoresis_duration_value'))?>:
                </h4>
                <div class="data-value">
                    <?php
                    if($element->iontophoresis_duration_value){
                    echo $element->iontophoresis_duration_value . " minutes";
                    }
                    ?>
                </div>
            </div>

            <div class="large-3 column">
                <h4 class="data-title">
                    <?php echo CHtml::encode($element->getAttributeLabel('riboflavin_preparation_id'))?>:
                </h4>
                <div class="data-value">
                    <?php
                    if($element->riboflavin_preparation_id){
                    $riboflavin_preparation_id = OphTrOperationnote_CXL_Riboflavin_Preparation::model()->find('id=?', $element->riboflavin_preparation_id);
                    echo $riboflavin_preparation_id->name;
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="large-3 column">
                <h4 class="data-title">
                    <?php echo CHtml::encode($element->getAttributeLabel('interval_between_drops_id'))?>:
                </h4>
                <div class="data-value">
                    <?php
                    $interval_between_drops_id = OphTrOperationnote_CXL_Interval_Between_Drops::model()->find('id=?',
                        $element->interval_between_drops_id);
                    echo $interval_between_drops_id->name;
                    ?>
                </div>
            </div>
            <div class="large-3 column">
                <h4 class="data-title">
                    <?php echo CHtml::encode($element->getAttributeLabel('soak_duration_range_id'))?>:
                </h4>
                <div class="data-value">
                    <?php
                    $soak_duration_range_id = OphTrOperationnote_CXL_Soak_Duration::model()->find('id=?',
                        $element->soak_duration_range_id);
                    echo $soak_duration_range_id->name;
                    ?>
                </div>
            </div>
            <div class="large-3 column">
                <h4 class="data-title">
                    <?php echo CHtml::encode($element->getAttributeLabel('uv_irradiance_range_id'))?>:
                </h4>
                <div class="data-value">
                    <?php
                    $uv_irradiance_range_id = OphTrOperationnote_CXL_UV_Irradiance_Range::model()->find('id=?',
                        $element->uv_irradiance_range_id);
                    echo $uv_irradiance_range_id->name . " mW/cm2";
                    ?>
                </div>
            </div>
            <div class="large-3 column">
                <h4 class="data-title">
                    <?php echo CHtml::encode($element->getAttributeLabel('total_exposure_time_id'))?>:
                </h4>
                <div class="data-value">
                    <?php
                    $total_exposure_time_id = OphTrOperationnote_CXL_Total_Exposure_Time::model()->find('id=?',
                        $element->total_exposure_time_id);
                    echo $total_exposure_time_id->name . ' minutes';
                    ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="large-3 column">
                <h4 class="data-title">
                    <?php echo CHtml::encode($element->getAttributeLabel('uv_total_energy_value'))?>:
                </h4>
                <div class="data-value">
                    <?php
                    if($element->uv_total_energy_value){
                    echo $element->uv_total_energy_value . ' J/cm2';
                    }
                    ?>
                </div>
            </div>
            <div class="large-3 column">
                <h4 class="data-title">
                    <?php echo CHtml::encode($element->getAttributeLabel('uv_pulse_duration_id'))?>:
                </h4>
                <div class="data-value">
                    <?php
                    $uv_pulse_duration_id = OphTrOperationnote_CXL_UV_Pulse_Duration::model()->find('id=?',
                        $element->uv_pulse_duration_id);
                    echo $uv_pulse_duration_id->name;
                    ?>
                </div>
            </div>
            <div class="large-3 column">
                <h4 class="data-title">
                    <?php echo CHtml::encode($element->getAttributeLabel('interpulse_duration_id'))?>:
                </h4>
                <div class="data-value">
                    <?php
                    $interpulse_duration_id = OphTrOperationnote_CXL_Interpulse_Duration::model()->find('id=?',
                        $element->interpulse_duration_id);
                    echo $interpulse_duration_id->name;
                    ?>
                </div>
            </div>
            <div class="large-3 column">
                </div>
            <div class="large-3 column">

            </div>
    </div>
        <div class="row">
            <div class="large-9 column">
                <h4 class="data-title">
                    <?php echo CHtml::encode($element->getAttributeLabel('cxl_comments'))?>:
                </h4>
                <div class="data-value">
                    <?php
                    echo $element->cxl_comments;
                    ?>
                </div>
            </div>
            <div class="large-3 column">
                <h4 class="data-title">
                    <?php echo CHtml::encode($element->getAttributeLabel('cxl_complications_id'))?>:
                </h4>
                <div class="data-value">
                    <?php
                    if($element->cxl_complications_id) {
                        echo $complicationName = OphTrOperationnote_CXL_Complications::model()->getName($element->cxl_complications_id);
                    }
                    ?>
                </div>
            </div>
            </div>
</section>
