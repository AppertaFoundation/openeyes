<?php

/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */


$form = $this->beginWidget('CActiveForm', array(
    'id'=>'merge-form',
    'enableAjaxValidation' => false,
    'enableClientValidation' => true,
    //'focus' => array($model,'firstName'),
    
)); ?>

<h1 class="badge">Patient Merge</h1>

 <div id="patientMergeWrapper" class="container content">
           
            <div class="row">
                <div class="large-5 column">
                    <h2 class="secondaryPatient">Secondary</h2>
                    <?php $this->renderPartial('_patient_details', array(
                            'model' => $model, 
                            'type' => 'secondary',
                            'mergeHandler' => isset($mergehandler) ? $mergehandler : null
                        ))?>
                </div>  

                <div class="large-2 column text-center">
                    <h2>INTO</h2>
                    <img class="into-arrow" src="<?= Yii::app()->assetManager->createUrl('img/_elements/graphic/right-black-arrow_128_30.png')?>" alt="OpenEyes logo" />
                </div>

                <div class="large-5 column">
                    <h2 class="primaryPatient">Primary</h2>
                    <?php $this->renderPartial('_patient_details', array(
                            'model' => $model, 
                            'type' => 'primary',
                            'mergeHandler' => isset($mergehandler) ? $mergehandler : null
                        ))?>
                </div>

            </div>
            <hr>
            <div class="row">
                <div class="large-5 column">Comment:
                    <?php echo CHTML::activeTextArea($model, "comment"); ?>
                </div>

            </div>

            <div class="row">
                <div class="large-3 column text-right large-offset-9">
                    <input class="warning" type="submit" value="Merge">
                </div>
            </div>
        <br>

    </div>
    
    <?php $this->endWidget(); ?>
      

