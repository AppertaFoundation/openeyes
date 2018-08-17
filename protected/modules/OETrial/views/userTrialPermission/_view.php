<?php
/* @var TrialController $this */
/* @var UserTrialPermission $data */

$canManageTrial = Trial::checkTrialAccess(Yii::app()->user, $data->trial_id, UserTrialPermission::PERMISSION_MANAGE);
?>

<tr data-permission-id="<?php echo $data->id; ?>">
    <?php echo CHtml::hiddenField('user_id', CHtml::encode($data->user_id), array('class' => 'user_id')); ?>
  <td>
      <?php echo CHtml::radioButton('pi_user_id', $data->trial->pi_user_id === $data->user_id,
          array('class' => 'trial-permission-pi-selector', 'disabled' => $canManageTrial ? false : true)); ?>

    <img id="pi-change-loader-<?php echo $data->user_id; ?>" class="loader"
         src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif') ?>"
         alt="loading..." style="display: none;"/>
  </td>
  <td>
      <?php echo CHtml::radioButton('coordinator_user_id', $data->trial->coordinator_user_id === $data->user_id,
          array('class' => 'trial-permission-coordinator-selector', 'disabled' => $canManageTrial ? false : true)); ?>
    <img id="coordinator-change-loader-<?php echo $data->user_id; ?>" class="loader"
         src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif') ?>"
         alt="loading..." style="display: none;"/>
  </td>
  <td>
      <?php echo CHtml::encode($data->user->getFullName()); ?>
  </td>
  <td>
      <?php echo CHtml::encode($data->role); ?>
  </td>
  <td>
      <?php echo UserTrialPermission::getPermissionOptions()[$data->permission]; ?>
  </td>
    <?php if ($canManageTrial): ?>
      <td>
          <?php if ($data->user_id !== Yii::app()->user->id): ?>
            <a href="#" rel="<?php echo $data->id; ?>" class="small removePermission">
              Remove
            </a>
          <?php endif; ?>
      </td>
    <?php endif; ?>
</tr>