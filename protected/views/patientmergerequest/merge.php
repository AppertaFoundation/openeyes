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

<div id="patientMergeWrapper" class="container content main-event">
    <div class="cols-3 column large-centered text-right large-offset-9">
        <section class="box dashboard">
            <?php
            echo CHtml::link(
                'Back to Patient Merge list',
                array('patientMergeRequest/index'),
                array('class' => 'button small')
            );
            ?>
        </section>
    </div>

    <form id="grid_header_form"
          class="element-fields full-width col-gap"
          action="<?php echo Yii::app()->createUrl('/patientMergeRequest/merge', array('id' => $model->id)) ?>"
          method="post">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
        <?= \CHtml::activeHiddenField($model, 'id') ?>
        <div class="cols-12 flex-layout">
            <div class="cols-5">
                <h2 class="secondaryPatient">Secondary</h2>
                <?php $this->renderPartial('//patientmergerequest/_patient_details',
                    array('model' => $model, 'type' => 'secondary', 'patient_identifier_types' => $patient_identifier_types)) ?>
            </div>

            <div class="cols-2" style="text-align: center">
                <h2>INTO</h2>
                <img class="into-arrow"
                     src="<?= Yii::app()->assetManager->createUrl('img/_elements/graphic/right-black-arrow_128_30.png') ?>"
                     alt="OpenEyes logo"/>
            </div>

            <div class="cols-5">
                <h2 class="primaryPatient">Primary</h2>
                <?php $this->renderPartial('//patientmergerequest/_patient_details',
                    array('model' => $model, 'type' => 'primary', 'patient_identifier_types' => $patient_identifier_types)) ?>
            </div>
        </div>
        <hr>
        <div class="cols-12 element-fields flex-layout flex-top col-gap">
            <div class="cols-3"></div>
            <div class="cols-6 element">
                Comment: <?= \CHtml::activeTextArea($model, 'comment', array('class' => 'cols-9')); ?>
                <br>
                <?php if ($personal_details_conflict_confirm && Yii::app()->user->checkAccess('Patient Merge')) : ?>
                    <div id="patientDataConflictConfirmation" class="data-group">
                        <div class="cols-10 large-offset-1 column alert-box with-icon warning">
                            <h2> Personal details are conflicting. </h2>
                            Please confirm you selected the right patients. <br>
                            Note: the primary patient's personal details will <strong>NOT</strong> be
                            overwritten.<br><br>
                            <label>
                                <?= \CHtml::checkBox('PatientMergeRequest[personal_details_conflict_confirm]', false); ?>
                                I hereby confirm that I selected the right patients.</label>
                        </div>
                    </div>
                <?php endif; ?>
                <?php $this->renderPartial('//base/_messages') ?>
                <?php if (Yii::app()->user->checkAccess('Patient Merge')) : ?>
                    <label>
                        <?= \CHtml::checkBox('PatientMergeRequest[confirm]', false); ?>
                        I declare under penalty of perjury I reviewed the details and I would like to proceed to merge.
                    </label>
                    <div class="flex-layout">
                        <div class="cols-6">
                            <input class="warning" type="submit" value="Merge">
                        </div>
                        <div class="cols-6">
                            <?= \CHtml::link(
                                'cancel',
                                array('patientMergeRequest/index'),
                                array('class' => 'button primary')
                            ); ?>
                        </div>
                    </div>

                <?php endif; ?>
            </div>
            <div class="cols-3"></div>
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