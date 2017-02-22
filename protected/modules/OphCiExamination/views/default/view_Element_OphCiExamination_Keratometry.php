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
<div class="element-data element-eyes row">
    <div class="element-eye right-eye column">
        <div class="data-row">
            <div class="data-value">
                <?php if ($element->hasRight()) {?>
                    <?php //  var_dump($element); ?>
                    <label><?php echo $element->getAttributeLabel('topographer_id')?>:</label><?php
                    $spec_micro = OEModule\OphCiExamination\models\OphCiExamination_Topographer_device::model()->find('id=?', $element->topographer_id);
                    echo $spec_micro->name;
                    ?><br/>
                    <label><?php echo $element->getAttributeLabel('topographer_scan_quality_id')?>:</label> <?php
                    $scan_quality = OEModule\OphCiExamination\models\OphCiExamination_Scan_Quality::model()->find('id=?', $element->topographer_scan_quality_id);
                    echo $scan_quality->name;
                    ?><br/>
                    <label><?php echo $element->getAttributeLabel('right_anterior_k1_value')?>:</label> <?php
                    echo $element->right_anterior_k1_value;
                    ?><br/>
                    <label><?php echo $element->getAttributeLabel('right_axis_anterior_k1_value')?>:</label> <?php
                    echo $element->right_coefficient_variation_value;
                    ?><br/>
                    <label><?php echo $element->getAttributeLabel('right_anterior_k2_value')?>:</label> <?php
                    echo $element->right_anterior_k2_value;
                    ?><br/>
                    <label><?php echo $element->getAttributeLabel('right_axis_anterior_k2_value')?>:</label> <?php
                    echo $element->right_axis_anterior_k2_value;
                    ?><br/>
                    <label><?php echo $element->getAttributeLabel('right_kmax_value')?>:</label> <?php
                    echo $element->right_kmax_value;
                    ?><br/>
                    <label><?php echo $element->getAttributeLabel('tomographer_id')?>:</label><?php
                    $spec_micro = OEModule\OphCiExamination\models\OphCiExamination_Tomographer_device::model()->find('id=?', $element->tomographer_id);
                    echo $spec_micro->name;
                    ?><br/>
                    <label><?php echo $element->getAttributeLabel('tomographer_scan_quality_id')?>:</label> <?php
                    $scan_quality = OEModule\OphCiExamination\models\OphCiExamination_Scan_Quality::model()->find('id=?', $element->tomographer_scan_quality_id);
                    echo $scan_quality->name;
                    ?><br/>
                    <label><?php echo $element->getAttributeLabel('right_posterior_k2_value')?>:</label> <?php
                    echo $element->right_posterior_k2_value;
                    ?><br/>
                    <label><?php echo $element->getAttributeLabel('right_thinnest_point_pachymetry_value')?>:</label> <?php
                    echo $element->right_thinnest_point_pachymetry_value;
                    ?><br/>
                    <label><?php echo $element->getAttributeLabel('right_b-a_index_value')?>:</label> <?php
                    echo $element->right_b-a_index_value;
                    ?><br/>
<?php
                } else {
                    ?>
                    Not recorded
                    <?php
                }?>
            </div>
        </div>
    </div>
    <div class="element-eye left-eye column">
        <div class="data-row">
            <div class="data-value">
                <?php if ($element->hasLeft()) {
                    ?>
                    <label><?php echo $element->getAttributeLabel('topographer_id')?>:</label><?php
                    $top_device = OEModule\OphCiExamination\models\OphCiExamination_Topographer_device::model()->find('id=?', $element->topographer_id);
                    echo $top_device->name;
                    ?><br/>
                    <label><?php echo $element->getAttributeLabel('tomographer_scan_quality_id')?>:</label> <?php
                    $scan_quality = OEModule\OphCiExamination\models\OphCiExamination_Scan_Quality::model()->find('id=?', $element->tomographer_scan_quality_id);
                    echo $scan_quality->name;
                    ?><br/>
                    <label><?php echo $element->getAttributeLabel('left_anterior_k1_value')?>:</label> <?php
                    echo $element->left_anterior_k1_value;
                    ?><br/>
                    <label><?php echo $element->getAttributeLabel('left_axis_anterior_k1_value')?>:</label> <?php
                    echo $element->left_coefficient_variation_value;
                    ?><br/>
                    <label><?php echo $element->getAttributeLabel('left_anterior_k2_value')?>:</label> <?php
                    echo $element->left_anterior_k2_value;
                    ?><br/>
                    <label><?php echo $element->getAttributeLabel('left_axis_anterior_k2_value')?>:</label> <?php
                    echo $element->left_axis_anterior_k2_value;
                    ?><br/>
                    <label><?php echo $element->getAttributeLabel('left_kmax_value')?>:</label> <?php
                    echo $element->left_kmax_value;
                    ?><br/>
                    <label><?php echo $element->getAttributeLabel('tomographer_id')?>:</label><?php
                    $tom_device = OEModule\OphCiExamination\models\OphCiExamination_Tomographer_device::model()->find('id=?', $element->tomographer_id);
                    echo $tom_device->name;
                    ?><br/>
                    <label><?php echo $element->getAttributeLabel('tomographer_scan_quality_id')?>:</label> <?php
                    $scan_quality = OEModule\OphCiExamination\models\OphCiExamination_Scan_Quality::model()->find('id=?', $element->tomographer_scan_quality_id);
                    echo $scan_quality->name;
                    ?><br/>
                    <label><?php echo $element->getAttributeLabel('left_posterior_k2_value')?>:</label> <?php
                    echo $element->left_posterior_k2_value;
                    ?><br/>
                    <label><?php echo $element->getAttributeLabel('left_thinnest_point_pachymetry_value')?>:</label> <?php
                    echo $element->left_thinnest_point_pachymetry_value;
                    ?><br/>
                    <label><?php echo $element->getAttributeLabel('left_b-a_index_value')?>:</label> <?php
                    echo $element->left_b-a_index_value;
                    ?><br/>
                    <?php
                } else {
                    ?>
                    Not recorded
                    <?php
                }?>
            </div>
        </div>
    </div>
</div>
