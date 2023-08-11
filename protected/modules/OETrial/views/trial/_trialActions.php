<?php
/* @var TrialController $this */
/* @var Trial $trial */
/* @var TrialPermission $permission */
?>
<nav class="oe-full-side-panel">
  <h3>Actions</h3>
  <ul>
        <li>
            <?=\CHtml::link('Go Back to Trials', Yii::app()->createUrl('OETrial/trial')) ?>
        </li>
        <?php if ($trial->is_open && $permission->can_edit) : ?>
            <?php if (\Yii::app()->user->checkAccess('TaskCaseSearch')) : ?>
          <li>
                <?php echo CHtml::link(
                    'Add Participants',
                    $this->createUrl('/OECaseSearch/caseSearch', array('trial_id' => $trial->id))
                ); ?>
        </li>
            <?php endif; ?>
            <?php if (CsvController::uploadAccess()) : ?>
        <li>
          <a href = <?=  Yii::app()->createURL(
              'csv/upload',
              array('context' => 'trialPatients', 'backuri' => '/OETrial/trial/view/' . $trial->id)
                    ) ?> >Upload trial patients</a>
        </li>
            <?php endif; ?>
        <?php endif; ?>
    <li>
        <?php echo CHtml::link(
            'Trial Permissions',
            $this->createUrl('permissions', array('id' => $trial->id))
        ); ?>
    </li>

        <?php if (Yii::app()->user->checkAccess('OprnGenerateReport')) : ?>
        <li>
            <?php echo CHtml::beginForm($this->createUrl('report/downloadReport')); ?>
            <?php echo CHtml::hiddenField('report-name', 'TrialCohort'); ?>
            <?php echo CHtml::hiddenField('report-filename', $trial->name); ?>
            <?php echo CHtml::hiddenField('trialID', $trial->id); ?>
            <?php echo CHtml::linkButton('Download Report'); ?>
            <?php echo CHtml::endForm(); ?>
        </li>
        <?php endif; ?>

        <?php if ($permission->can_manage) : ?>
        <li>
            <?php echo CHtml::beginForm(array('delete'), 'post', array('id' => 'delete-trial-form')); ?>
            <?php echo CHtml::hiddenField('id', $trial->id); ?>
            <?php echo CHtml::linkButton('Delete Trial', array('id' => 'delete-trial-submit')); ?>
            <?php echo CHtml::endForm(); ?>
        </li>
        <?php endif; ?>
  </ul>
</nav>
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
