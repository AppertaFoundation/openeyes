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
	<div class="large-4 column">
        
        <div class="large-12 column">     
            <?= $element->getAttributeLabel('blood_pressure')?>: <?php echo (!empty($element->blood_pressure_systolic)) ? $element->blood_pressure_systolic.'/'. $element->blood_pressure_diastolic.' mmHg' :  '' ?> 
        </div>
       
        <div class="large-12 column">
            <?= $element->getAttributeLabel('blood_glucose')?>: <?php echo (!empty($element->blood_glucose)) ? $element->blood_glucose.' mmol/l': '' ?> 
        </div>
       
        <div class="large-12 column">
            <?= $element->getAttributeLabel('weight')?>: <?php echo (!empty($element->weight)) ? $element->weight.' kg': '' ?> 
        </div>
    </div>
    
    <div class="large-4 column">
        
        <div class="large-12 column">
            <?= $element->getAttributeLabel('o2_sat')?>: <?php echo (!empty($element->o2_sat)) ? $element->o2_sat.' %' : '' ?>
        </div>
      
        <div class="large-12 column">
            <?= $element->getAttributeLabel('hba1c')?>: <?php echo (!empty($element->hba1c)) ? $element->hba1c.' mmol/mol' : '' ?> 
        </div>
  
        <div class="large-12 column" >
            <?= $element->getAttributeLabel('height')?>: <?php echo (!empty($element->height)) ? $element->height.' cm' : '' ?>
        </div> 
    </div>
    
    <div class="large-4 column">
        <div class="large-12 column">
            <?= $element->getAttributeLabel('pulse')?>: <?php echo (!empty($element->pulse)) ? $element->pulse.' BPM' : ''; ?> 
        </div>

        <div class="large-12 column">&nbsp;</div>
        
        <div class="large-12 column">
            BMI:
            <div id="bmi-container" style="display:inline-block;">
                <?php 
                    if(ceil($element->weight) > 0 && ceil($element->height) > 0){
                        echo $element->bmiCalculator( $element->weight, $element->height) ;
                    } else {
                        echo 'N/A';
                    }
                ?>
            </div>  
        </div>
       
    </div>
</div>