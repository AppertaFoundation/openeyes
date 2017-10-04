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
<div class="element-data">
    <div class="data-row">
        <div class="data-value column large-4">
            <?php echo $element->getAttributeLabel('specular_microscope_id')?>:
            <?php
            echo OEModule\OphCiExamination\models\OphCiExamination_Specular_Microscope::model()->getName($element->specular_microscope_id);
            ?>
        </div>
        <div class="data-value column large-4">
            <?php echo $element->getAttributeLabel('scan_quality_id')?>:
            <?php
            echo OEModule\OphCiExamination\models\OphCiExamination_Scan_Quality::model()->getName($element->scan_quality_id);
            ?>
        </div>
        <div class="data-value column large-4">
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