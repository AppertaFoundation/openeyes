<?php
/* @var $this PatientController */
?>
<section class="box patient-info js-toggle-container">
  <h3 class="box-title">Trials:</h3>
  <a href="#" class="toggle-trigger toggle-hide js-toggle">
		<span class="icon-showhide">
			Show/hide this section
		</span>
  </a>
  <a href="#" class="toggle-trigger toggle-hide js-toggle">
      <span class="icon-showhide">
        Show/hide this section
      </span>
  </a>

  <div class="js-toggle-body">

    <table class="plain patient-data">
      <thead>
      <tr>
        <th>Trial</th>
        <th><?php echo Trial::model()->getAttributeLabel('coordinator_user_id') ?></th>
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
      foreach ($this->patient->trials as $trialPatient):
          ?>
        <tr>
          <td><?php if (Yii::app()->user->checkAccess('TaskViewTrial')): ?>
                  <?php echo CHtml::link(CHtml::encode($trialPatient->trial->name),
                      Yii::app()->controller->createUrl('/OETrial/trial/permissions',
                          array('id' => $trialPatient->trial_id))); ?>
              <?php else: ?>
                  <?php echo CHtml::encode($trialPatient->trial->name); ?>
              <?php endif; ?>
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
          <td><?php echo $trialPatient->treatmentType->name; ?></td>
          <td><?php echo $trialPatient->status->name; ?></td>
          <td><?php echo $trialPatient->trial->trialType->name; ?></td>
          <td><?php echo $trialPatient->trial->getStartedDateForDisplay(); ?></td>
          <td><?php echo $trialPatient->trial->getClosedDateForDisplay(); ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
