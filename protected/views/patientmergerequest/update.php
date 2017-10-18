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
        
        <div class="row">
            <div class="large-3 column large-centered text-right large-offset-9">
                <section class="box dashboard">
                <?php 
                    echo CHtml::link('Back to Patient Merge list', array('patientMergeRequest/index'), array('class' => 'button small'));
                ?>
                </section>
            </div>
        </div>
        <div class="row">
            <div class="large-7 column large-centered">
                <?php $this->renderPartial('//patientmergerequest/_patient_search', array('patient_type' => 'patient'))?>
            </div>
        </div>
        <form id="grid_header_form" action="<?php echo Yii::app()->createUrl('/patientMergeRequest/update', array('id' => $model->id))?>" method="post">
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
                    <button type="button" id="swapPatients"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAYCAYAAACbU/80AAABdklEQVRIS8XWv0vUcRzH8cehIog0Obio/QMuTk45KQYOidBQk+DkIopbazQEkkODERzh0tIQQQjh0uYgTq2RQzQKgSKhQ7yPO/h6nPe9z5fv3fczv96f15PP+9enpv9nGo/xrpNVrf/+HuIXtrHf7jdIgPB+gVdZiEEDhHcABEjjVAEQvm+wUxQg6NcTamcYMx30B9gs8gJRSFsJAN2k9SoBbvC8CMAqFhJe4EGHlP3DU3wpApDg3ZC25kAr7hpP8C2vCOdwjotUxzZ9FuASK/ie14bzOMIsfpcE8BfLOMkbRI/wFeOYKgngFEs4yxvFi/iMsaawDIAJTOJH3jKK3HzCaEZYBkDXDLa6YA0fMdKm/oCrhBo4br5gzyEB8AyHGOo56n7hHnZT7gmA99hICeqiLQQQEG9jMXS4+CduE+DqeJ2gv7OOg76xIqsowpbny+xnoaQ50FMXZEWx7wMkzsDasJ0yUhEpqQwggKIoYyr+SSmqVO1/0Hw+DMYnP1MAAAAASUVORK5CYII="><br>Swap</button>
                </div>  

                <div class="large-5 column">
                    <h2 class="primaryPatient">Primary</h2>
                    <?php $this->renderPartial('//patientmergerequest/_patient_details', array('model' => $model, 'type' => 'primary'))?>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="large-5 column">Comment:
                    <?php echo CHTML::activeTextArea($model, 'comment'); ?>
                </div>
            </div>
            <br>
            <?php $this->renderPartial('//base/_messages')?>
            <?php if ($personal_details_conflict_confirm && Yii::app()->user->checkAccess('Patient Merge')):?>
                <div id="patientDataConflictConfirmation" class="row">
                    <div class="large-10 large-offset-1 column alert-box with-icon warning">
                        <h2> Personal details are conflicting. </h2>
                        Please confirm you selected the right patients. <br>
                        Note, the primary patient's personal details will <strong>NOT</strong> be overwritten.<br><br>
                        <label>
                        <?php echo CHTML::checkBox('PatientMergeRequest[personal_details_conflict_confirm]', false); ?> I hereby confirm that I selected the right patients.</label>
                    </div>
                </div>
                <div class="row">
                    <div class="large-12 column text-left">
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="large-3 column text-right large-offset-9">
                    <?php echo CHtml::link('cancel', array('patientMergeRequest/index'), array('class' => 'button primary')); ?> 
                    <input type="submit" value="Save" class="secondary button" id="mergeRequestUpdate">
                    
                </div>
            </div>
        </form>
        <br>

    </div>
<script>
    
    patientMerge.patients.primary = JSON.parse('<?php echo $primary_patient_JSON; ?>');
    patientMerge.patients.primary['all-episodes'] = $('<textarea />').html(patientMerge.patients.primary['all-episodes']).text();
    
    patientMerge.patients.secondary = JSON.parse('<?php echo $secondary_patient_JSON; ?>');
    patientMerge.patients.secondary['all-episodes'] = $('<textarea />').html(patientMerge.patients.secondary['all-episodes']).text();
    
    patientMerge.updateDOM('primary');
    patientMerge.updateDOM('secondary');
    
</script>
