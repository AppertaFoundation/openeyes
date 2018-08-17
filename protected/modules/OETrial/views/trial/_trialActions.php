<?php
/* @var TrialController $this */
/* @var Trial $trial */
/* @var TrialPermission $permission */
?>

<div class="large-3 column">
  <div class="box generic">

      <?php if ($permission->can_view): ?>
        <p>
            <?php if ($this->action->id === 'view'): ?>
              View Trial Details
            <?php else: ?>
              <span class="highlight">
              <?php echo CHtml::link('View Trial Details',
                  $this->createUrl('view', array('id' => $trial->id))); ?>
            </span>
            <?php endif; ?>
        </p>
      <?php endif; ?>

      <?php if ($permission->can_edit): ?>
        <p>
          <span class="highlight">
            <?php echo CHtml::link('Edit Trial Details',
                $this->createUrl('update', array('id' => $trial->id))); ?>
          </span>
        </p>
      <?php endif; ?>

      <?php if ($this->action->id === 'permissions'): ?>
        <p>
          Trial Permissions
        </p>
      <?php else: ?>
        <p>
          <span class="highlight">
            <?php echo CHtml::link('Trial Permissions',
                $this->createUrl('permissions', array('id' => $trial->id))); ?>
          </span>
        </p>
      <?php endif; ?>

      <?php if ($trial->is_open && $permission->can_edit): ?>
        <p>
          <span class="highlight">
            <?php echo CHtml::link('Add Participants',
                $this->createUrl('/OECaseSearch/caseSearch', array('trial_id' => $trial->id))); ?>
          </span>
        </p>
      <?php endif; ?>

      <?php if (Yii::app()->user->checkAccess('OprnGenerateReport')): ?>
          <?php echo CHtml::beginForm($this->createUrl('report/downloadReport')); ?>
        <p>
          <span class="highlight">
              <?php echo CHtml::hiddenField('report-name', 'TrialCohort'); ?>
              <?php echo CHtml::hiddenField('report-filename', $trial->name); ?>
              <?php echo CHtml::hiddenField('trialID', $trial->id); ?>

              <?php echo CHtml::linkButton('Download Report'); ?>

          </span>
        </p>
          <?php echo CHtml::endForm(); ?>
      <?php endif; ?>

      <?php if ($permission->can_manage): ?>
          <?php echo CHtml::beginForm(array('delete'), 'post', array('id' => 'delete-trial-form')); ?>
        <p>
          <span class="highlight">
          <?php echo CHtml::hiddenField('id', $trial->id); ?>
          <?php echo CHtml::linkButton('Delete Trial', array('id' => 'delete-trial-submit')); ?>
          </span>
        </p>
          <?php echo CHtml::endForm(); ?>
      <?php endif; ?>

  </div>
</div>


<script type="application/javascript">

  $(document).ready(function () {

    $('#delete-trial-submit').click(function (e) {
      var confirmDialog = new OpenEyes.UI.Dialog.Confirm({
        title: "Delete Trial",
        content: "Are you sure you want to delete this trial? "
      });

      confirmDialog.content.on('click', '.ok', function () {
        $('#delete-trial-form').submit();
      });

      confirmDialog.open();
      return false;
    });
  });
</script>
