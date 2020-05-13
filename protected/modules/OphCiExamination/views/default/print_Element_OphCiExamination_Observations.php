<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 *  You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div class="element-data data-group">
    <div class="cols-4 column">
        
        <div class="cols-7 column">
            <label>
                <?= $element->getAttributeLabel('blood_pressure_systolic')?>:
            </label>
        </div>
        <div class="cols-5 column">
            <label><?= $element->blood_pressure_systolic ?> mmHg</label>
        </div>
       
        <div class="cols-7 column">
            <label>
                <?= $element->getAttributeLabel('blood_glucose')?>:
            </label>
        </div>
        <div class="cols-5 column">
            <label><?= $element->blood_glucose ?> mmol/l</label>
        </div>
        
        <div class="cols-7 column">
            <label>
                <?= $element->getAttributeLabel('weight')?>:
            </label>
        </div>
        <div class="cols-5 column">
            <label><?= $element->weight ?> kg</label>
        </div>
    </div>
  <div class="cols-4 column">
        
        <div class="cols-7 column">
            <label>
                <?= $element->getAttributeLabel('blood_pressure_diastolic')?>:
            </label>
        </div>
        <div class="cols-5 column">
            <label><?= $element->blood_pressure_diastolic ?> mmHg</label>
        </div>
        
        <div class="cols-7 column">
            <label>
                <?= $element->getAttributeLabel('hba1c')?>:
            </label>
        </div>
        <div class="cols-5 column">
            <label><?= $element->hba1c ?> mmol/mol</label>
        </div>
       
        <div class="cols-7 column">
            <label>
                BMI:
            </label>
        </div>
        <div class="cols-5 column" id="bmi-container">
            <label><?= $element->bmiCalculator($element->weight, $element->height) ?></label>
        </div>    
    </div>
  <div class="cols-4 column">
        <div class="cols-7 column">
            <label>
                <?= $element->getAttributeLabel('o2_sat')?>:
            </label>
        </div>
        <div class="cols-5 column">
            <label><?= $element->o2_sat ?> %</label>
        </div>
       
        <div class="cols-7 column" >
            <label>
                <?= $element->getAttributeLabel('height')?>:
            </label>
        </div>
        <div class="cols-5 column" id="bmi-height">
            <label><?= $element->height ?> cm</label>
        </div>
      
        <div class="cols-7 column">
            <label>
                <?= $element->getAttributeLabel('pulse')?>:
            </label>
        </div>
        <div class="cols-5 column">
            <label><?= $element->pulse ?> BPM</label>
        </div>
      
    </div>
</div>