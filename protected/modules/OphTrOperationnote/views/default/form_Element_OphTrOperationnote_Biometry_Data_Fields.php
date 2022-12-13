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
$biometry_data = [
    'k1' => $element->{"k1_{$side}"},
    'k2' => $element->{"k2_{$side}"},
    'axis_k1' => $element->{"k1_axis_{$side}"},
    'axis_k2' => $element->{"k2_axis_{$side}"},
];
?>

<div class="element-data" data-biometry-data='<?= json_encode($biometry_data) ?>'>
    <div class="data-group">
      <input type="hidden" id="Element_OphTrOperationnote_Biometry_id_hidden"
             name="Element_OphTrOperationnote_Biometry[id]" value="<?php echo $element->id; ?>">

        <table class="cols-11 large-text">
            <colgroup>
                <col class="cols-3">
                <col class="cols-2">
                <col class="cols-1">
            </colgroup>
            <tbody>
            <tr>
                <td>
                    <div class="data-label">
                        <?= \CHtml::encode($element->getAttributeLabel('lens_id_' . $side)) ?>
                    </div>
                </td>
                <td>
                    <div class="data-value"
                         id="lens_<?= $side ?>"><?= $element->{'lens_display_name_' . $side} ? $element->{'lens_display_name_' . $side} : 'None' ?>
                        <input type="hidden" class="js-selected_lens" value="<?php echo $element->{'lens_id_' . $side} ?>">
                    </div>
                </td>
                <td colspan="2"></td>
                <td>
                    <?php $this->widget('EyeLateralityWidget', array('laterality' => $side)) ?>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="data-label">
                        <?=\CHtml::encode($element->getAttributeLabel('power')) ?>
                    </div>
                </td>
                <td>
                    <div class="data-value large-text highlighter orange js-iol-display"><?=\CHtml::encode($element->{'iol_power_' . $side}) ?></div>
                </td>
                <td>
                    <div class="data-label">
                        <?=\CHtml::encode($element->getAttributeLabel('predicted_refraction_' . $side)) ?>
                    </div>
                </td>
                <td>
                    <div class="data-value js-predicted-refraction"
                         id="predicted_refraction_<?php echo $side ?>">
                    <?php echo isset($element->{'predicted_refraction_' . $side}) ? $element->{'predicted_refraction_' . $side} : 'None' ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <?=\CHtml::encode($element->getAttributeLabel('formula_')) ?>
                </td>
                <td>
                    <?php echo $element->{'formula_' . $side} ? $element->{'formula_' . $side} : 'None' ?>
                </td>
                <td>
                    <?=\CHtml::encode($element->getAttributeLabel('lens_acon_' . $side)) ?>
                </td>
                <td>
                    <?php echo $element->{'lens_acon_' . $side} ? $this->formatAconst($element->{'lens_acon_' . $side}) : 'None' ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?=\CHtml::encode($element->getAttributeLabel('axial_length_' . $side)) ?>
                </td>
                <td>
                    <?=\CHtml::encode($element->{'axial_length_' . $side}) ?>
                </td>
                <td colspan="2"></td>
                <td>
                    <?php $this->widget('EyeLateralityWidget', array('laterality' => $side)) ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?=\CHtml::encode($element->getAttributeLabel('snr_' . $side)) ?>
                </td>
                <td>
                    <?=\CHtml::encode($element->{'snr_' . $side}) ?>
                </td>
                <td colspan="2"></td>
                <td>
                    <?php $this->widget('EyeLateralityWidget', array('laterality' => $side)) ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?=\CHtml::encode($element->getAttributeLabel('k1_' . $side)) ?>
                </td>
                <td>
                    <?=\CHtml::encode($element->{'k1_' . $side}) ?>D
                </td>
                <td>@</td>
                <td><?=\CHtml::encode($element->{'k1_axis_' . $side}) ?>&deg;</td>
                <td><?php $this->widget('EyeLateralityWidget', array('laterality' => $side)) ?></td>
            </tr>
            <tr>
                <td>
                    &Delta;<?=\CHtml::encode($element->getAttributeLabel('delta_k_' . $side)) ?>
                </td>
                <td>
                    <?php if (($element->{'delta_k_' . $side}) > 0) {
                        echo '+';
                    }
                    echo CHtml::encode($element->{'delta_k_' . $side}) ?>D
                </td>
                <td>@</td>
                <td><?=\CHtml::encode($element->{'delta_k_axis_' . $side}) ?>&deg;</td>
                <td><?php $this->widget('EyeLateralityWidget', array('laterality' => $side)) ?></td>
            </tr>
            <tr>
                <td>
                    <?=\CHtml::encode($element->getAttributeLabel('k2_' . $side)) ?>
                </td>
                <td>
                    <?=\CHtml::encode($element->{'k2_' . $side}) ?>D
                </td>
                <td>
                    @
                </td>
                <td>
                    <?=\CHtml::encode($element->{'k2_axis_' . $side}) ?>&deg;
                </td>
                <td>
                    <?php $this->widget('EyeLateralityWidget', array('laterality' => $side)) ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?=\CHtml::encode($element->getAttributeLabel('acd_' . $side)) ?>
                </td>
                <td>
                    <?=\CHtml::encode($element->{'acd_' . $side}) ?>mm
                </td>
                <td></td>
                <td></td>
                <td>
                    <?php $this->widget('EyeLateralityWidget', array('laterality' => $side)) ?>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="data-group">
        <table class="label-value last-left">
            <colgroup>
                <col class="cols-6">
            </colgroup>
            <tbody>
            <tr>
                <td>
                    <div class="data-label">
                        <?=\CHtml::encode($element->getAttributeLabel('status_' . $side)) ?>
                    </div>
                </td>
                <td>
                    <div class="data-value">
                        <span class="large-text">
                            <?=\CHtml::encode($element->{'status_' . $side}) ?>
                        </span>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="data-label"><b>Comments</b></div>
                </td>
                <td>
                    <div class="data-value"><?=\CHtml::encode($element->{'comments'}) ?></div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
