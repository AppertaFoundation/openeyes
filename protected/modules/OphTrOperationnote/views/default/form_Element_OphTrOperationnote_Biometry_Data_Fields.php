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

    <div class="row data-row">
        <input type="hidden" id="Element_OphTrOperationnote_Biometry_id_hidden"
               name="Element_OphTrOperationnote_Biometry[id]" value="<?php echo $element->id; ?>">

        <div class="large-2 column">
            <div class="data-label"><b><?php echo CHtml::encode($element->getAttributeLabel('lens_id_'.$side)) ?></b>
            </div>
        </div>
        <div class="large-10 column end">
            <div class="data-value iolDisplayHighlited"
                 id="lens_<?php echo $side ?>"><?php echo $element->{'lens_display_name_'.$side} ? $element->{'lens_display_name_'.$side} : 'None' ?>
            <input type="hidden" class="selected_lens" value="<?php echo $element->{'lens_id_'.$side} ?>"></div>
        </div>
    </div>
    <div class="row field-row">
        <div class="large-2 column">
            <div class="data-label">
                <b><?php echo CHtml::encode($element->getAttributeLabel('iol_power_'.$side)) ?></b></div>
        </div>
        <div class="large-4 column end">
            <div class="data-value iolDisplayHighlited iolDisplay"><?php echo CHtml::encode($element->{'iol_power_'.$side}) ?></div>
        </div>
        <div class="large-2 column">
            <div class="data-label">
                <b><?php echo CHtml::encode($element->getAttributeLabel('predicted_refraction_'.$side)) ?></b></div>
        </div>
        <div class="large-4 column">
            <div class="data-value predictedRefraction"
                 id="predicted_refraction_<?php echo $side ?>"><?php echo $element->{'predicted_refraction_'.$side} ? $element->{'predicted_refraction_'.$side} : 'None' ?></div>
        </div>
    </div>
    <div class="row data-row">
        <div class="large-2 column">
            <div class="data-label">
                <b><?php echo CHtml::encode($element->getAttributeLabel('formula_'.$side)) ?></b></div>
        </div>
        <div class="large-4 column">
            <div class="data-value"
                 id="formula_<?php echo $side ?>"><?php echo $element->{'formula_'.$side} ? $element->{'formula_'.$side} : 'None' ?>&nbsp;</div>
        </div>
        <div class="large-2 column">
            <div class="data-label">
                <b><?php echo CHtml::encode($element->getAttributeLabel('lens_acon_'.$side)) ?></b></div>
        </div>
        <div class="large-4 column">
            <div class="data-value"
                 id="acon_<?php echo $side ?>"><?php echo $element->{'lens_acon_'.$side} ? $this->formatAconst($element->{'lens_acon_'.$side}) : 'None' ?></div>
        </div>
    </div>

    <div class="row field-row">
        <div class="large-12 column">&nbsp;</div>
    </div>

    <div class="row field-row">
        <div class="large-2 column">
            <div class="data-label">
                <b><?php echo CHtml::encode($element->getAttributeLabel('axial_length_'.$side)) ?></b></div>
        </div>
        <div class="large-4 column">
            <div class="data-value"
                 id="axial_length_<?php echo $side ?>"><?php echo CHtml::encode($element->{'axial_length_'.$side}) ?>
                &nbsp;mm
            </div>
        </div>

        <div class="large-2 column">
            <div class="data-label"><b><?php echo CHtml::encode($element->getAttributeLabel('snr_'.$side)) ?></b>
            </div>
        </div>
        <div class="large-4 column">
            <div class="data-value"
                 id="snr_<?php echo $side ?>"><?php echo CHtml::encode($element->{'snr_'.$side}) ?></div>
        </div>
    </div>

    <div class="row field-row">
        <div class="large-2 column">
            <div class="data-label"><b><?php echo CHtml::encode($element->getAttributeLabel('k1_'.$side)) ?></b></div>
        </div>
        <div class="large-4 column">
            <div class="data-value" id="k1_<?php echo $side ?>"><?php echo CHtml::encode($element->{'k1_'.$side}) ?>
                &nbsp;D&nbsp;<b>@</b>&nbsp;<?php echo CHtml::encode($element->{'axis_k1_'.$side}) ?>&deg;</div>
        </div>
        <div class="large-2 column">
            <div class="data-label">
                <b>&Delta;<?php echo CHtml::encode($element->getAttributeLabel('delta_k_'.$side)) ?></b></div>
        </div>
        <div class="large-4 column">
            <div class="data-value"
                 id="k1_<?php echo $side ?>"><?php if (($element->{'delta_k_'.$side}) > 0) echo '+'; echo CHtml::encode($element->{'delta_k_'.$side}) ?>&nbsp;D&nbsp;<b>@</b>&nbsp;<?php echo CHtml::encode($element->{'delta_k_axis_'.$side}) ?>&deg;
            </div>
        </div>
    </div>
    <div class="row field-row">
        <div class="large-2 column">
            <div class="data-label"><b><?php echo CHtml::encode($element->getAttributeLabel('k2_'.$side)) ?></b></div>
        </div>
        <div class="large-4 column">
            <div class="data-value" id="k1_<?php echo $side ?>"><?php echo CHtml::encode($element->{'k2_'.$side}) ?>
                &nbsp;D&nbsp;<b>@</b>&nbsp;<?php echo CHtml::encode($element->{'k2_axis_'.$side}) ?>&deg;</div>
        </div>
        <div class="large-2 column">
            <div class="data-label"><b><?php echo CHtml::encode($element->getAttributeLabel('acd_'.$side)) ?></b>
            </div>
        </div>
        <div class="large-4 column">
            <div class="data-value" id="k1_<?php echo $side ?>"><?php echo CHtml::encode($element->{'acd_'.$side}) ?>
                &nbsp;mm
            </div>
        </div>
    </div>

    <div class="row field-row">
        <div class="large-2 column">
            <div class="data-label"><b><?php echo CHtml::encode($element->getAttributeLabel('status_'.$side)) ?></b>
            </div>
        </div>
        <div class="large-10 column end">
            <div class="data-value"><?php echo CHtml::encode($element->{'status_'.$side}) ?></div>
        </div>
    </div>
    <div class="row field-row">
        <div class="large-2 column">
            <div class="data-label"><b>Comments</b></div>
        </div>
        <div class="large-10 column end">
            <div class="data-value"><?php echo CHtml::encode($element->{'comments'}) ?></div>
        </div>
    </div>
</div>
