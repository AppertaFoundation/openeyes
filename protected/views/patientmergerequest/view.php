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

    <div id="patientMergeWrapper" class="container content">
        <?php $this->renderPartial('//base/_messages')?>
        <div class="row">
            <div class="large-4 column large-centered text-right large-offset-8">
                <section class="box dashboard">
                <?php 
                    echo CHtml::link('Back to Patient Merge list', array('patientMergeRequest/index'), array('class' => 'button small')).' ';
                    echo CHtml::link('edit', array('patientMergeRequest/update', 'id' => $model->id), array('class' => 'button small secondary'));
                    if (Yii::app()->user->checkAccess('Patient Merge')) {
                        echo ' '.CHtml::link('merge', array('patientMergeRequest/merge', 'id' => $model->id), array('class' => 'button small warning '));
                    }
                ?>
                </section>
            </div>
        </div>
        
        <form id="grid_header_form" action="<?php echo Yii::app()->createUrl('/patientMergeRequest/merge', array('id' => $model->id))?>" method="post">
            <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken?>" />
            <?php echo CHTML::activeHiddenField($model, 'id') ?>
            <div class="row">
                <div class="large-5 column">
                    <h2 class="secondaryPatient">Secondary</h2>
                    <?php $this->renderPartial('//patientmergerequest/_patient_details', array('model' => $model, 'type' => 'secondary'))?>
                </div>  

                <div class="large-2 column text-center">
                    <h2>INTO</h2>
                    <img class="into-arrow" src="<?= Yii::app()->assetManager->createUrl('img/_elements/graphic/right-black-arrow_128_30.png')?>" alt="OpenEyes logo" />
                </div>  

                <div class="large-5 column">
                    <h2 class="primaryPatient">Primary</h2>
                    <?php $this->renderPartial('//patientmergerequest/_patient_details', array('model' => $model, 'type' => 'primary'))?>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="large-5 column">Comment:
                    <?php echo CHTML::activeTextArea($model, 'comment', array('disabled' => 'disabled')); ?>
                </div>
            </div>
            <br>
        </form>
        <br>
        
    </div>
