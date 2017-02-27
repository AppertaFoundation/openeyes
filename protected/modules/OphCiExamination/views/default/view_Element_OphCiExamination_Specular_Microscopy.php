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
<div class="element-data">
    <div class="data-row">
        <div class="data-value">
            <?php echo $element->getAttributeLabel('specular_microscope_id')?>:
<?php
$spec_micro = OEModule\OphCiExamination\models\OphCiExamination_Specular_Microscope::model()->find('id=?', $element->specular_microscope_id);
echo $spec_micro->name;
?><br/>
            <?php echo $element->getAttributeLabel('scan_quality_id')?>:
 <?php
$scan_quality = OEModule\OphCiExamination\models\OphCiExamination_Scan_Quality::model()->find('id=?', $element->scan_quality_id);
echo $scan_quality->name;
?><br/>
        </div>
    </div>
</div>
<div class="element-data element-eyes row">
    <div class="element-eye right-eye column">
        <?php if ($element->hasRight()) {?>
        <div class="row">
            <div class="large-6 column data-value">
                    <?php echo $element->getAttributeLabel('right_endothelial_cell_density_value')?>:
            </div>
            <div class="large-5 column data-value">
                <?php echo $element->right_endothelial_cell_density_value;
                    ?>
            </div>
        </div>
        <div class="row">
            <div class="large-6 column data-value">
                    <?php echo $element->getAttributeLabel('right_coefficient_variation_value')?>:
            </div>
            <div class="large-5 column data-value">
                    <?php
                    echo $element->right_coefficient_variation_value;
                    ?>
            </div>
        </div>
<?php
                } else {
                    ?>
                    Not recorded
                    <?php
                }?>
        </div>

    <div class="element-eye left-eye column">
        <?php if ($element->hasLeft()) {?>
        <div class="row">
            <div class="large-6 column data-value">
                <?php echo $element->getAttributeLabel('left_endothelial_cell_density_value')?>:
            </div>
            <div class="large-5 column data-value">
                <?php echo $element->left_endothelial_cell_density_value;
                ?>
            </div>
        </div>
        <div class="row">
            <div class="large-6 column data-value">
                <?php echo $element->getAttributeLabel('left_coefficient_variation_value')?>:
            </div>
            <div class="large-5 column data-value">
                <?php
                echo $element->left_coefficient_variation_value;
                ?>
            </div>
        </div>
        <?php
        } else {
            ?>
            Not recorded
            <?php
        }?>
    </div>
</div>