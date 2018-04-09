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
<div class="element-fields">
    <div class="large-4 column">
        
        <div class="large-5 column">
            <label for="<?= CHtml::modelName($element).'_blood_pressure_systolic';?>">
                <?= $element->getAttributeLabel('blood_pressure')?>:
            </label>
        </div>
        <div class="large-7 column">
            <?= CHtml::activeTextField($element,'blood_pressure_systolic', array('autocomplete' => Yii::app()->params['html_autocomplete'], 'style' => 'width:40px; display:inline-block;')); ?> /
            <?= CHtml::activeTextField($element,'blood_pressure_diastolic', array('autocomplete' => Yii::app()->params['html_autocomplete'] , 'style' => 'width:40px; display:inline-block;')); ?> 
            <label style="display:inline-block; font-size: 0.8125rem;">mmHg</label>
        </div>
        
        <div class="large-5 column">
            <label for="<?= CHtml::modelName($element).'_blood_glucose';?>">
                <?= $element->getAttributeLabel('blood_glucose')?>:
            </label>
        </div>
        <div class="large-3 column">
            <?= CHtml::activeTextField($element,'blood_glucose', array('autocomplete' => Yii::app()->params['html_autocomplete'])); ?>
        </div>
        <label class="large-3 column">mmol/l</label>
        
        <div class="large-5 column">
            <label for="<?= CHtml::modelName($element).'_weight';?>">
                <?= $element->getAttributeLabel('weight')?>:
            </label>
        </div>
        <div class="large-3 column bmi-keyup-event">
            <?= CHtml::activeTextField($element,'weight', array('autocomplete' => Yii::app()->params['html_autocomplete'])); ?>
        </div>
        <label class="large-3 column">kg</label>
        
        
    </div>
    
    <div class="large-4 column">
        
        <div class="large-5 column">
            <label for="<?= CHtml::modelName($element).'_o2_sat';?>">
                <?= $element->getAttributeLabel('o2_sat')?>:
            </label>
        </div>
        <div class="large-3 column">
            <?= CHtml::activeTextField($element,'o2_sat', array('autocomplete' => Yii::app()->params['html_autocomplete'])); ?>
        </div>
        <label class="large-3 column">%</label>
        
        <div class="large-5 column">
            <label for="<?= CHtml::modelName($element).'_hba1c';?>">
                <?= $element->getAttributeLabel('hba1c')?>:
            </label>
        </div>
        <div class="large-3 column">
            <?= CHtml::activeTextField($element,'hba1c', array('autocomplete' => Yii::app()->params['html_autocomplete'])); ?>
        </div>
        <label class="large-3 column">mmol/mol</label>
        
        <div class="large-5 column">
            <label for="<?= CHtml::modelName($element).'_height';?>">
                <?= $element->getAttributeLabel('height')?>:
            </label>
        </div>
        <div class="large-3 column bmi-keyup-event">
            <?= CHtml::activeTextField($element,'height', array('autocomplete' => Yii::app()->params['html_autocomplete'])); ?>
        </div>
        <label class="large-3 column">cm</label>
        
    </div>
    
    <div class="large-4 column">
        
        <div class="large-5 column">
            <label for="<?= CHtml::modelName($element).'_pulse';?>">
                <?= $element->getAttributeLabel('pulse')?>:
            </label>
        </div>
        <div class="large-3 column">
            <?= CHtml::activeTextField($element,'pulse', array('autocomplete' => Yii::app()->params['html_autocomplete'])); ?>
        </div>
        <label class="large-3 column">BPM</label>
        
        <div class="large-12 column">&nbsp;</div>
        
        <div class="large-5 column">
            <label>
                BMI:
            </label>
        </div>
        <div class="large-3 column" id="bmi-container">
            
        </div>
        <label class="large-3 column">&nbsp;</label>
       
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        var heightElement = $( "#OEModule_OphCiExamination_models_Element_OphCiExamination_Observations_height" );
        var weightElement = $( "#OEModule_OphCiExamination_models_Element_OphCiExamination_Observations_weight" );
        
        height = heightElement.val();
        weight = weightElement.val();  
        getBMI( height, weight);
            
        $('.bmi-keyup-event input[type="text"]').keyup(function() {
            height = heightElement.val();
            weight = weightElement.val();
            getBMI( height, weight);  
        });
        
        function getBMI(height , weight){
            bmi = 0;
            if((height > 0) && (weight > 0)){
                bmi = bmi_calculator( weight , height);
                result = bmi.toFixed(2) || 'N/A';
            } else {
                result = 'N/A';
            }
            
            $('#bmi-container').text( result ); 
        }
      
    });
</script>

