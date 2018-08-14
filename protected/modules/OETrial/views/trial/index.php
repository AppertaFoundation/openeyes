<?php
/* @var TrialController $this */
/* @var CActiveDataProvider $interventionTrialDataProvider */
/* @var CActiveDataProvider $nonInterventionTrialDataProvider */
/* @var string $sort_by */
/* @var string $sort_dir */

?>
<div class="oe-full-header flex-layout">
  <div class="title wordcaps">Trials</div>
</div>
<div class="oe-full-content subgrid oe-worklists">

    <?php if (Yii::app()->user->hasFlash('success')): ?>
      <div class="alert-box with-icon success">
          <?php echo Yii::app()->user->getFlash('success'); ?>
      </div>
    <?php endif; ?>

  <nav class="oe-full-side-panel">
    <h3>Filter by Date</h3>
    <div class="flex-layout">
      <input class="cols-5" placeholder="from" type="text">
      <input class="cols-5" placeholder="to" type="text">
    </div>

    <h3>Actions</h3>
      <?php if (Yii::app()->user->checkAccess('TaskCreateTrial')): ?>
        <ul>
          <li>
              <?= CHtml::link('Create a New Trial', array('create')) ?>
          </li>
            <?php if (\CsvController::uploadAccess()): ?>
              <li>
                  <?= CHtml::link('Upload trials', Yii::app()->createURL('csv/upload', array('context' => 'trials'))) ?>
              </li>
              <li>
                  <?= CHtml::link('Upload trial patients',
                      Yii::app()->createURL('csv/upload', array('context' => 'trialPatients'))) ?>
              </li>
            <?php endif ?>
        </ul>
      <?php endif; ?>
  </nav>

  <main class="oe-full-main">
      <?php
      $this->renderPartial('_trial_list', array(
          'dataProvider' => $interventionTrialDataProvider,
          'title' => 'Intervention Trials',
          'sort_by' => $sort_by,
          'sort_dir' => $sort_dir,
      ));
      ?>
      <?php
      $this->renderPartial('_trial_list', array(
          'dataProvider' => $nonInterventionTrialDataProvider,
          'title' => 'Non-Intervention Trials',
          'sort_by' => $sort_by,
          'sort_dir' => $sort_dir,
      ));
      ?>
  </main>
</div>

<script type="text/javascript">
  $('.js-trial-list .clickable').click(function () {
    window.location.href = '<?= $this->createUrl('view')?>/' + $(this).attr('id').match(/[0-9]+/);
    return false;
  });
</script>