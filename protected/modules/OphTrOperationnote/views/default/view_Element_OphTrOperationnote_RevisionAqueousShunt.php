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
<section class="element">
    <h3 class="element-title highlight">Revision of aqueous shunt</h3>
    <div class="element-data">
        <div class="row data-row">
            <div class="large-4 column">
                <h4 class="data-title"><?=\CHtml::encode($element->getAttributeLabel('plate_pos_id'))?></h4>
                <div class="data-value"><?=$element->getPlatePosById($element->plate_pos_id)?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <h4 class="data-title"><?=\CHtml::encode($element->getAttributeLabel('is_shunt_explanted'))?></h4>
                <div class="data-value"><?=$element->is_shunt_explanted ? 'Yes' : 'No';?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <h4 class="data-title"><?=\CHtml::encode($element->getAttributeLabel('final_tube_position_id'))?></h4>
                <div class="data-value"><?=!$element->final_tube_position_id ? 'N/A' : $element->getTubePosById($element->final_tube_position_id);?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <h4 class="data-title"><?=\CHtml::encode($element->getAttributeLabel('intraluminal_stent_id'))?></h4>
                <div class="data-value"><?=$element->getRipcordSutureById($element->intraluminal_stent_id);?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <h4 class="data-title"><?=\CHtml::encode($element->getAttributeLabel('is_visco_in_ac'))?></h4>
                <div class="data-value"><?=$element->is_visco_in_ac ? 'Yes' : 'No';?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <h4 class="data-title"><?=\CHtml::encode($element->getAttributeLabel('is_flow_tested'))?></h4>
                <div class="data-value"><?=$element->is_flow_tested ? 'Yes' : 'No';?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <h4 class="data-title"><?=\CHtml::encode($element->getAttributeLabel('comments'))?></h4>
                <div class="data-value"><?=\CHtml::encode($element->comments)?></div>
            </div>
        </div>
    </div>
</section>
