<?php
/* @var $this PatientController */
?>
<div class="flex-layout flex-top" style="width: 100%;">
  <div class="oe-popup-overflow quicklook-data-groups" style="width: 100%;">
    <div class="subtitle">Past and Current Trials</div>
    <div class="group">
      <div class="label"></div>
      <div class="data">
          <?php if (count($this->patient->trials) === 0) { ?>
            <div class="nil-recorded">No trials recorded.</div>
          <?php } else { ?>
            <table>
              <thead>
              <tr>
                <th>Trial</th>
                <th><?= Trial::model()->getAttributeLabel('coordinator_user_id') ?></th>
                <th>Treatment</th>
                <th>Trial Status</th>
                <th>Trial Type</th>
                <th>Date Started</th>
                <th>Date Ended</th>
              </tr>
              </thead>
              <tbody>
              <?php
              /* @var TrialPatient $trialPatient */
              foreach ($this->patient->trials as $trialPatient): //
                  ?>
                <tr>
                  <td><?php if (Yii::app()->user->checkAccess('TaskViewTrial')) {
                          echo CHtml::link(CHtml::encode($trialPatient->trial->name),
                              Yii::app()->controller->createUrl('/OETrial/trial/permissions',
                                  array('id' => $trialPatient->trial_id)));
                      } else {
                          echo CHtml::encode($trialPatient->trial->name);
                      } ?>
                  </td>
                  <td>
                      <?php
                      $coordinator = $trialPatient->trial->coordinatorUser;
                      if ($coordinator !== null) {
                          echo CHtml::encode($coordinator->last_name . ', ' . $coordinator->first_name);
                      } else {
                          echo 'N/A';
                      }
                      ?>
                  </td>
                  <td><?= $trialPatient->treatmentType->name; ?></td>
                  <td><?= $trialPatient->status->name; ?></td>
                  <td><?= $trialPatient->trial->trialType->name; ?></td>
                  <td><?= $trialPatient->trial->getStartedDateForDisplay(); ?></td>
                  <td><?= $trialPatient->trial->getClosedDateForDisplay(); ?></td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          <?php } ?>
      </div>
    </div>
  </div>
