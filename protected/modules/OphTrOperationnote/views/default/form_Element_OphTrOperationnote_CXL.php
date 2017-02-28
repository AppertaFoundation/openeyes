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
<div class="element-fields">
    <?php echo $form->radioButtons($element, 'protocol_id',  'OphTrOperationnote_CXL_Protocol', $element->protocol_id)?>
    <?php echo $form->radioButtons($element, 'epithelial_removal_method_id',
        'OphTrOperationnote_CXL_Epithelial_Removal_Method', $element->epithelial_removal_method_id)?>
    <?php echo $form->radioButtons($element, 'epithelial_removal_diameter_id',
        'OphTrOperationnote_CXL_Epithelial_Removal_Diameter', $element->epithelial_removal_diameter_id)?>
    <?php echo $form->radioButtons($element, 'riboflavin_preparation_id',
        'OphTrOperationnote_CXL_Riboflavin_Preparation', $element->riboflavin_preparation_id)?>
    <?php echo $form->radioButtons($element, 'interval_between_drops_id',
        'OphTrOperationnote_CXL_Interval_Between_Drops', $element->interval_between_drops_id)?>
    <?php echo $form->radioButtons($element, 'soak_duration_range_id',
        'OphTrOperationnote_CXL_Soak_Duration', $element->soak_duration_range_id)?>
    <?php echo $form->radioButtons($element, 'uv_irradiance_range_id',
        'OphTrOperationnote_CXL_UV_Irradiance', $element->uv_irradiance_range_id)?>
    <?php echo $form->radioButtons($element, 'total_exposure_time_id',
        'OphTrOperationnote_CXL_Total_Exposure_Time', $element->total_exposure_time_id)?>
    <?php echo $form->radioButtons($element, 'uv_pulse_duration_range_id',
        'OphTrOperationnote_CXL_UV_Pulse_Duration', $element->uv_pulse_duration_range_id)?>
    <?php echo $form->radioButtons($element, 'interpulse_duration_range_id',
        'OphTrOperationnote_CXL_Interpulse_Duration', $element->interpulse_duration_range_id)?>
    <?php // echo $form->radioBoolean($element, 'brilliant_blue')?>
    <?php // echo $form->textField($element, 'other_dye', array(), array(), array_merge($form->layoutColumns, array('field' => 3)))?>
    <?php // echo $form->textArea($element, 'comments', array('rows' => 4), false, array(), array_merge($form->layoutColumns, array('field' => 6)))?>
</div>

