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
                <th>Study Coordinator</th>
                <th>Treatment</th>
                <th>Trial Status</th>
                <th>Accepted/Rejected Date</th>
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
                  <td>
                      <?php if (!is_null($trialPatient->trial->getUserPermission(Yii::app()->user->id)) && (Yii::app()->user->checkAccess('TaskViewTrial'))) {
                          echo CHtml::link(CHtml::encode($trialPatient->trial->name),
                              Yii::app()->controller->createUrl('/OETrial/trial/view',
                                  array('id' => $trialPatient->trial_id)));
                      } else {
                          echo CHtml::encode($trialPatient->trial->name);
                      } ?>
                  </td>
                  <td>
                      <?php
                      $coordinators = $trialPatient->trial->getTrialStudyCoordinators();
                      if (sizeof($coordinators)) {
                        foreach ($coordinators as $item){
                          echo $item->user->getFullName().'<br>';
                        }
                      } else {
                          echo 'N/A';
                      }
                      ?>
                  </td>
                  <td><?= $trialPatient->treatmentType->name; ?></td>
                  <td><?= $trialPatient->status->name; ?></td>
                  <td><?php
                      if (isset($trialPatient->status_update_date)) {
                          echo Helper::formatFuzzyDate($trialPatient->status_update_date);
                      } ?></td>
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
