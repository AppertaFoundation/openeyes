<?php
/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
$firm = Firm::model()->findByPk($element->consultant_in_charge_of_this_cvi_id);
?>

<div class="element-data">
    <div class="row data-row">
        <div class="large-2 column">
            <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('site_id')) ?>:</div>
        </div>
        <div class="large-4 column end">
            <div class="data-value"><?php echo $element->site ? CHtml::encode($element->site->name) : '-' ?></div>
        </div>
        <div class="large-2 column">
            <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('consultant_in_charge_of_this_cvi_id')) ?>:</div>
        </div>
        <div class="large-4 column end">
            <div class="data-value"><?php echo $element->consultant_in_charge_of_this_cvi_id ? CHtml::encode($firm->getNameAndSubspecialty()) : '-' ?></div>
        </div>
    </div>
</div>