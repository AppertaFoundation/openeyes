<?php
/* @var TrialController $this */
/* @var CActiveDataProvider $interventionTrialDataProvider */
/* @var CActiveDataProvider $nonInterventionTrialDataProvider */
/* @var string $sort_by */
/* @var string $sort_dir */

?>

<h1 class="badge">Trials</h1>

<div class="row">
  <div class="large-9 column">

    <div class="box admin">

        <?php if (Yii::app()->user->hasFlash('success')): ?>
          <div class="alert-box with-icon success">
              <?php echo Yii::app()->user->getFlash('success'); ?>
          </div>
        <?php endif; ?>

        <?php
        $this->widget('zii.widgets.CBreadcrumbs', array(
            'links' => $this->breadcrumbs,
        ));
        ?>

        <?php
        $this->renderPartial('_trialList', array(
            'dataProvider' => $interventionTrialDataProvider,
            'title' => 'Intervention Trials',
            'sort_by' => $sort_by,
            'sort_dir' => $sort_dir,
        ));
        ?>

      <hr/>

        <?php
        $this->renderPartial('_trialList', array(
            'dataProvider' => $nonInterventionTrialDataProvider,
            'title' => 'Non-Intervention Trials',
            'sort_by' => $sort_by,
            'sort_dir' => $sort_dir,
        ));
        ?>
    </div>

  </div><!-- /.large-9.column -->
    <?php if (Yii::app()->user->checkAccess('TaskCreateTrial')): ?>
      <div class="large-3 column">
        <div class="box generic">
          <p><span class="highlight"><?php echo CHtml::link('Create a New Trial', array('create')) ?></span></p>
            <?php if (\CsvController::uploadAccess()): ?>
              <p><span class="highlight"><?php echo CHtml::link('Upload trials'
                          , Yii::app()->createURL('csv/upload', array('context' => 'trials'))); ?></span></p>
              <p><span class="highlight"><?php echo CHtml::link('Upload trial patients'
                          , Yii::app()->createURL('csv/upload', array('context' => 'trialPatients'))); ?></span></p>
            <?php endif ?>
        </div>
      </div>
    <?php endif; ?>
</div>


<script type="text/javascript">
  $('#patient-grid tr.clickable').click(function () {
    window.location.href = '<?php echo $this->createUrl('view')?>/' + $(this).attr('id').match(/[0-9]+/);
    return false;
  });
</script>