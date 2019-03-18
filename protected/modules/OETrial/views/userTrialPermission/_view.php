<?php
/**
 * @var TrialController $this
 * @var TrialPermission $permission
 * @var UserTrialAssignment $data
 */

$permission = TrialController::getCurrentUserPermission();

?>

<tr class="js-user-trial-permission" data-permission-id="<?= $data->id; ?>">
    <?= CHtml::hiddenField('user_id', CHtml::encode($data->user_id), array('class' => 'user_id')); ?>

  <td>
      <?= CHtml::encode($data->user->getFullName()); ?>
  </td>
  <td>
      <?= CHtml::encode($data->role); ?>
  </td>
  <td>
      <?= $data->trialPermission->name ?>
  </td>
  <td>
      <?= CHtml::checkBox('principal_investigator', $data->is_principal_investigator,
        array(
          'class' => 'is_principal_investigator',
          'data-user' => $data->user_id,
          'data-trial' => $data->trial_id,
          'disabled' => !$permission || !$permission->can_manage)); ?>
    <span id="pi-change-loader-<?= $data->user_id; ?>" class="js-spinner-as-icon" style="display: none;">
      <i class="spinner as-icon"></i>
    </span>
  </td>
  <td>
    <?= CHtml::checkBox('principal_investigator', $data->is_study_coordinator,
      array(
        'class'=>'is_coordinator',
        'data-user' => $data->user_id,
        'data-trial' => $data->trial_id,
        'disabled' => !$permission || !$permission->can_manage)); ?>

    <span id="coordinator-change-loader-<?= $data->user_id; ?>" class="js-spinner-as-icon" style="display: none;">
      <i class="spinner as-icon"></i>
    </span>
  </td>

    <?php if ($permission && $permission->can_manage): ?>
      <td>
          <?php if ($data->user_id !== Yii::app()->user->id): ?>
            <span class="js-remove-permission">
              <i class="oe-i trash"></i>
            </span>
            <span id="remove-permission-loader-<?= $data->id ?>" class="js-spinner-as-icon"
                  style="visibility: hidden;">
              <i class="spinner as-icon"></i>
            </span>
          <?php endif; ?>
      </td>
    <?php endif; ?>
</tr>