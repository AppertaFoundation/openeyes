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
        <div class="large-3 column">
            <div class="data-label">
                <?php echo $element->getAttributeLabel('tomographer_id')?>:
            </div>
        </div>
        <div class="large-3 column">
            <div class="data-value">
                <?php
                echo OEModule\OphCiExamination\models\OphCiExamination_Tomographer_Device::model()->getName($element->tomographer_id);
                ?>
            </div>
        </div>
        <div class="large-6 column">
            </div>
    </div>
</div>

<div class="element-data element-eyes row">
    <div class="element-eye right-eye column">
        <?php if ($element->hasRight()) {?>
                <div class="row">
                    <div class="large-6 column data-value">
                        <?php echo $element->getAttributeLabel('right_anterior_k1_value')?>:
                    </div>
                    <div class="large-5 column data-value">
                    <?php
                    echo $element->right_anterior_k1_value;
                    ?>
                    </div>
                </div>
                <div class="row">
                <div class="large-6 column data-value">
                    <?php echo $element->getAttributeLabel('right_anterior_k2_value')?>:
                </div>
                    <div class="large-5 column data-value">
                        <?php
                        echo $element->right_anterior_k2_value;
                    ?>
                    </div>
                </div>
            <div class="row">
                <div class="large-6 column data-value">
                    <?php echo $element->getAttributeLabel('right_quality_front')?>:
                </div>
                <div class="large-5 column data-value">
                    <?php
                    if($element->right_quality_front){
                        echo OEModule\OphCiExamination\models\OphCiExamination_CXL_Quality_Score::model()->getName($element->right_quality_front);
                    }
                    ?>
                </div>
            </div>
        <div class="row">
            <div class="large-6 column data-value">
                    <?php echo $element->getAttributeLabel('right_axis_anterior_k1_value')?>:
            </div>
            <div class="large-5 column data-value">
                <?php
                echo $element->right_axis_anterior_k1_value;
                    ?>
            </div>
        </div>
        <div class="row">
            <div class="large-6 column data-value">
                    <?php echo $element->getAttributeLabel('right_axis_anterior_k2_value')?>:
            </div>
            <div class="large-5 column data-value">
            <?php
                    echo $element->right_axis_anterior_k2_value;
            ?>
            </div>
        </div>
            <div class="row">
                <div class="large-6 column data-value">
                    <?php echo $element->getAttributeLabel('right_quality_back')?>:
                </div>
                <div class="large-5 column data-value">
                    <?php
                    if($element->right_quality_back){
                        echo OEModule\OphCiExamination\models\OphCiExamination_CXL_Quality_Score::model()->getName($element->right_quality_back);
                    }
                    ?>
                </div>
            </div>
        <div class="row">
            <div class="large-6 column data-value">
                    <?php echo $element->getAttributeLabel('right_kmax_value')?>:
            </div>
            <div class="large-5 column data-value">
                <?php
                    echo $element->right_kmax_value;
                ?>
            </div>
        </div>
        <div class="row">
            <div class="large-6 column data-value">
            <?php echo $element->getAttributeLabel('right_thinnest_point_pachymetry_value')?>:
            </div>
            <div class="large-5 column data-value">
                <?php
                    echo $element->right_thinnest_point_pachymetry_value;
                ?>
                </div>
        </div>
        <div class="row">
            <div class="large-6 column data-value">
                    <?php echo $element->getAttributeLabel('right_ba_index_value')?>:
            </div>
            <div class="large-5 column data-value">
                <?php
                    echo $element->right_ba_index_value;
                ?>
            </div>
        </div>
            <div class="row">
                <div class="large-6 column data-value">
                    <?php echo $element->getAttributeLabel('right_flourescein_value')?>:
                </div>
                <div class="large-5 column data-value">
                    <?php
                    echo yesOrNo($element->right_flourescein_value);
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="large-6 column data-value">
                    <?php echo $element->getAttributeLabel('right_cl_removed')?>:
                </div>
                <div class="large-5 column data-value">
                    <?php
                    if($element->right_cl_removed){
                        echo OEModule\OphCiExamination\models\OphCiExamination_CXL_CL_Removed::model()->getName($element->right_cl_removed);
                    }
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
                        <?php echo $element->getAttributeLabel('left_anterior_k1_value')?>:
                    </div>
                    <div class="large-5 column data-value">
                        <?php
                        echo $element->left_anterior_k1_value;
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="large-6 column data-value">
                        <?php echo $element->getAttributeLabel('left_anterior_k2_value')?>:
                    </div>
                    <div class="large-5 column data-value">
                        <?php
                        echo $element->left_anterior_k2_value;
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="large-6 column data-value">
                        <?php echo $element->getAttributeLabel('left_quality_front')?>:
                    </div>
                    <div class="large-5 column data-value">
                        <?php
                        if($element->left_quality_front){
                            echo OEModule\OphCiExamination\models\OphCiExamination_CXL_Quality_Score::model()->getName($element->left_quality_front);
                        }
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="large-6 column data-value">
                        <?php echo $element->getAttributeLabel('left_axis_anterior_k1_value')?>:
                    </div>
                    <div class="large-5 column data-value">
                        <?php
                        echo $element->left_axis_anterior_k1_value;
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="large-6 column data-value">
                        <?php echo $element->getAttributeLabel('left_axis_anterior_k2_value')?>:
                    </div>
                    <div class="large-5 column data-value">
                        <?php
                        echo $element->left_axis_anterior_k2_value;
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="large-6 column data-value">
                        <?php echo $element->getAttributeLabel('left_quality_back')?>:
                    </div>
                    <div class="large-5 column data-value">
                        <?php
                        if($element->left_quality_back){
                            echo OEModule\OphCiExamination\models\OphCiExamination_CXL_Quality_Score::model()->getName($element->left_quality_back);
                        }
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="large-6 column data-value">
                        <?php echo $element->getAttributeLabel('left_kmax_value')?>:
                    </div>
                    <div class="large-5 column data-value">
                        <?php
                        echo $element->left_kmax_value;
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="large-6 column data-value">
                        <?php echo $element->getAttributeLabel('left_thinnest_point_pachymetry_value')?>:
                    </div>
                    <div class="large-5 column data-value">
                        <?php
                        echo $element->left_thinnest_point_pachymetry_value;
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="large-6 column data-value">
                        <?php echo $element->getAttributeLabel('left_ba_index_value')?>:
                    </div>
                    <div class="large-5 column data-value">
                        <?php
                        echo $element->left_ba_index_value;
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="large-6 column data-value">
                        <?php echo $element->getAttributeLabel('left_flourescein_value')?>:
                    </div>
                    <div class="large-5 column data-value">
                        <?php
                        echo yesOrNo($element->left_flourescein_value);
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="large-6 column data-value">
                        <?php echo $element->getAttributeLabel('left_cl_removed')?>:
                    </div>
                    <div class="large-5 column data-value">
                        <?php
                        if($element->left_cl_removed){
                            echo OEModule\OphCiExamination\models\OphCiExamination_CXL_CL_Removed::model()->getName($element->left_cl_removed);
                        }
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
