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
    <?php $this->renderPartial('//base/_messages') ?>
  <div class="element-fields full-width flex-layout flex-top col-gap element">
      <div class="cols-3">
          <section class="box dashboard">
                <?php
                echo CHtml::link(
                    'Back to Patient Merge list',
                    array('patientMergeRequest/index'),
                    array('class' => 'button small')
                ) . ' ';
                echo CHtml::link(
                    'edit',
                    array('patientMergeRequest/update', 'id' => $model->id),
                    array('class' => 'button small secondary')
                );
                if (Yii::app()->user->checkAccess('Patient Merge')) {
                    echo ' ' . CHtml::link(
                        'merge',
                        array('patientMergeRequest/merge', 'id' => $model->id),
                        array('class' => 'button small warning ')
                    );
                }
                ?>
          </section>
      </div>
  </div>

  <form id="grid_header_form"
        class="element-fields full-width col-gap"
        action="<?php echo Yii::app()->createUrl('/patientMergeRequest/merge', array('id' => $model->id)) ?>"
        method="post">
    <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
        <?=\CHtml::activeHiddenField($model, 'id') ?>
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
    <div class="cols-5">Comment:
        <?=\CHtml::activeTextArea($model, 'comment', array('disabled' => 'disabled')); ?>
    </div>
    <br>
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
