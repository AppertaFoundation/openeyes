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

<section class="element">

  <header class="sub-element-header">
    <h3 class="element-title highlight"><?php echo $element->elementType->name ?></h3>
  </header>
  <div class="element-data">
    <div class="data-group">
      <div class="cols-3 column">
        <h4 class="data-title">
            <?=\CHtml::encode($element->getAttributeLabel('protocol_id')) ?>:
        </h4>
        <div class="data-value">
            <?php
            echo OphTrOperationnote_CXL_Protocol::model()->getName($element->protocol_id);
            ?>
        </div>
      </div>
      <div class="cols-3 column">
        <h4 class="data-label">
            <?=\CHtml::encode($element->getAttributeLabel('device_id')) ?>:
        </h4>
        <div class="data-value">
            <?php
            echo OphTrOperationnote_CXL_Device::model()->getName($element->device_id);
            ?>
        </div>
      </div>
      <div class="cols-3 column">
        <h4 class="data-label">
            <?=\CHtml::encode($element->getAttributeLabel('epithelial_status_id')) ?>:
        </h4>
        <div class="data-value">
            <?php
            if ($element->epithelial_status_id) {
                echo OphTrOperationnote_CXL_Epithelial_Status::model()->getName($element->epithelial_status_id);
            }
            ?>
        </div>
      </div>
      <div class="cols-3 column">
        <h4 class="data-label">
            <?=\CHtml::encode($element->getAttributeLabel('epithelial_removal_method_id')) ?>:
        </h4>
        <div class="data-value">
            <?php
            if ($element->epithelial_removal_method_id) {
                echo OphTrOperationnote_CXL_Epithelial_Removal_Method::model()->getName($element->epithelial_removal_method_id);
            }
            ?>
        </div>
      </div>
      <div class="cols-3 column">
        <h4 class="data-label">
            <?=\CHtml::encode($element->getAttributeLabel('epithelial_removal_diameter_id')) ?>:
        </h4>
        <div class="data-value">
            <?php
            echo OphTrOperationnote_CXL_Epithelial_Removal_Diameter::model()->getName($element->epithelial_removal_diameter_id);
            ?>
        </div>
      </div>
      <div class="cols-3 column">
        <h4 class="data-label">
            <?=\CHtml::encode($element->getAttributeLabel('mitomycin_c')) ?>:
        </h4>
        <div class="data-value">
            <?php
            if ($element->mitomycin_c) {
                echo OphTrOperationnote_CXL_Mitomycin::model()->getName($element->mitomycin_c);
            }
            ?>
        </div>
      </div>
      <div class="cols-3 column">
        <h4 class="data-label">
            <?=\CHtml::encode($element->getAttributeLabel('iontophoresis_id')) ?>:
        </h4>
        <div class="data-value">
            <?php
            if ($element->iontophoresis_id) {
                echo OphTrOperationnote_CXL_Iontophoresis::model()->getName($element->iontophoresis_id);
            }
            ?>
        </div>
      </div>
      <div class="cols-3 column">
        <h4 class="data-label">
            <?=\CHtml::encode($element->getAttributeLabel('iontophoresis_current_value')) ?>:
        </h4>
        <div class="data-value">
            <?php
            if ($element->iontophoresis_current_value) {
                echo $element->iontophoresis_current_value . ' mA';
            }
            ?>
        </div>
      </div>
      <div class="cols-3 column">
        <h4 class="data-label">
            <?=\CHtml::encode($element->getAttributeLabel('iontophoresis_duration_value')) ?>:
        </h4>
        <div class="data-value">
            <?php
            if ($element->iontophoresis_duration_value) {
                echo $element->iontophoresis_duration_value . " minutes";
            }
            ?>
        </div>
      </div>

    </div>
    <div class="data-group">
      <div class="cols-3 column">
        <h4 class="data-label">
            <?=\CHtml::encode($element->getAttributeLabel('riboflavin_preparation_id')) ?>:
        </h4>
        <div class="data-value">
            <?php
            if ($element->riboflavin_preparation_id) {
                echo OphTrOperationnote_CXL_Riboflavin_Preparation::model()->getName($element->riboflavin_preparation_id);
            }
            ?>
        </div>
      </div>
      <div class="cols-3 column">
        <h4 class="data-label">
            <?=\CHtml::encode($element->getAttributeLabel('interval_between_drops_id')) ?>:
        </h4>
        <div class="data-value">
            <?php
            echo OphTrOperationnote_CXL_Interval_Between_Drops::model()->getName($element->interval_between_drops_id);
            ?>
        </div>
      </div>
      <div class="cols-3 column">
        <h4 class="data-label">
            <?=\CHtml::encode($element->getAttributeLabel('soak_duration_range_id')) ?>:
        </h4>
        <div class="data-value">
            <?php
            echo OphTrOperationnote_CXL_Soak_Duration::model()->getName($element->soak_duration_range_id);
            ?>
        </div>
      </div>
      <div class="cols-3 column">
        <h4 class="data-label">
            <?=\CHtml::encode($element->getAttributeLabel('uv_irradiance_range_id')) ?>:
        </h4>
        <div class="data-value">
            <?php
            echo OphTrOperationnote_CXL_UV_Irradiance_Range::model()->getName($element->uv_irradiance_range_id) . " mW/cm2";
            ?>
        </div>
      </div>
    </div>
    <div class="data-group">
      <div class="cols-3 column">
        <h4 class="data-label">
            <?=\CHtml::encode($element->getAttributeLabel('total_exposure_time_id')) ?>:
        </h4>
        <div class="data-value">
            <?php
            echo OphTrOperationnote_CXL_Total_Exposure_Time::model()->getName($element->total_exposure_time_id) . ' minutes';
            ?>
        </div>
      </div>
      <div class="cols-3 column">
        <h4 class="data-label">
            <?=\CHtml::encode($element->getAttributeLabel('uv_total_energy_value')) ?>:
        </h4>
        <div class="data-value">
            <?php
            if ($element->uv_total_energy_value) {
                echo $element->uv_total_energy_value . ' J/cm2';
            }
            ?>
        </div>
      </div>
      <div class="cols-3 column">
        <h4 class="data-label">
            <?=\CHtml::encode($element->getAttributeLabel('uv_pulse_duration_id')) ?>:
        </h4>
        <div class="data-value">
            <?php
            echo OphTrOperationnote_CXL_UV_Pulse_Duration::model()->getName($element->uv_pulse_duration_id);
            ?>
        </div>
      </div>
      <div class="cols-3 column">
        <h4 class="data-label">
            <?=\CHtml::encode($element->getAttributeLabel('interpulse_duration_id')) ?>:
        </h4>
        <div class="data-value">
            <?php
            echo OphTrOperationnote_CXL_Interpulse_Duration::model()->getName($element->interpulse_duration_id);
            ?>
        </div>
      </div>
      <div class="cols-3 column">
      </div>
      <div class="cols-3 column">

      </div>
    </div>
    <div class="cols-9 column">
        <h4 class="data-label">
            <?=\CHtml::encode($element->getAttributeLabel('cxl_comments')) ?>:
        </h4>
        <div class="data-value">
            <?php
            echo $element->cxl_comments;
            ?>
        </div>
      </div>
</section>
