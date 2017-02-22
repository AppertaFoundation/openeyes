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
                    <?php  var_dump($element); ?>
                    Specular Microscope: <?php
                    $spec_micro = OEModule\OphCiExamination\models\OphCiExamination_Specular_Microscope::model()->find('id=?', $element->right_specular_microscope_id);
                    echo $spec_micro->name;
                    ?><br/>
                    Scan Quality: <?php
                    $scan_quality = OEModule\OphCiExamination\models\OphCiExamination_Scan_Quality::model()->find('id=?', $element->right_scan_quality_id);
                    echo $scan_quality->name;
                    ?><br/>
                    Endothelial Cell Density: <?php
                    echo $element->right_endothelial_cell_density_value;
                    ?><br/>
                    Coefficient of Variation: <?php
                    echo $element->right_coefficient_variation_value;
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
                    Specular Microscope: <?php
                    $spec_micro = OEModule\OphCiExamination\models\OphCiExamination_Specular_Microscope::model()->find('id=?', $element->left_specular_microscope_id);
                    echo $spec_micro->name;
                    ?><br/>
                    Scan Quality: <?php
                    $scan_quality = OEModule\OphCiExamination\models\OphCiExamination_Scan_Quality::model()->find('id=?', $element->left_scan_quality_id);
                    echo $scan_quality->name;
                    ?><br/>
                    Endothelial Cell Density: <?php
                    echo $element->left_endothelial_cell_density_value;
                    ?><br/>
                    Coefficient of Variation: <?php
                    echo $element->left_coefficient_variation_value;
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
