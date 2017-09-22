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
<?php
    $cvi_api = Yii::app()->moduleAPI->get('OphCoCvi');
    if ($cvi_api) {
        echo $cvi_api->renderAlertForVA($this->patient, $element, true);
    }
?>

<?php echo CHtml::hiddenField('element_id', $element->id, array('class' => 'element_id')); ?>

<div class="element-data element-eyes row">
    <div class="element-eye right-eye column">
        <?php if ($element->hasRight()) {
            ?>
            <?php if ($element->getCombined('right')) {
                ?>
                <div class="data-row">
                    <div class="data-value">
                        <?php echo $element->unit->name ?>
                    </div>
                </div>
                <div class="data-row">
                    <div class="data-value">
                        <?php echo $element->getCombined('right') ?>
                    </div>
                </div>
                <?php
            } else {
                ?>
                <div class="data-row">
                    <div class="data-value">
                        Not recorded
                        <?php if ($element->right_unable_to_assess) {
                            ?>
                            (Unable to assess<?php if ($element->right_eye_missing) {
                                ?>, eye missing<?php
                            }
                            ?>)
                            <?php
                        } elseif ($element->right_eye_missing) {
                            ?>
                            (Eye missing)
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <?php
            }
            ?>
            <?php
        } else {
            ?>
            <div class="data-row">
                <div class="data-value">
                    Not recorded
                </div>
            </div>
            <?php
        } ?>
    </div>
    <div class="element-eye left-eye column">
        <?php if ($element->hasLeft()) {
            ?>
            <?php if ($element->getCombined('left')) {
                ?>
                <div class="data-row">
                    <div class="data-value">
                        <?php echo $element->unit->name ?>
                    </div>
                </div>
                <div class="data-row">
                    <div class="data-value">
                        <?php echo $element->getCombined('left') ?>
                    </div>
                </div>
                <?php
            } else {
                ?>
                <div class="data-row">
                    <div class="data-value">
                        Not recorded
                        <?php if ($element->left_unable_to_assess) {
                            ?>
                            (Unable to assess<?php if ($element->left_eye_missing) {
                                ?>, eye missing<?php
                            }
                            ?>)
                            <?php
                        } elseif ($element->left_eye_missing) {
                            ?>
                            (Eye missing)
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <?php
            }
            ?>
            <?php
        } else {
            ?>
            <div class="data-row">
                <div class="data-value">
                    Not recorded
                </div>
            </div>
            <?php
        } ?>
    </div>
</div>
