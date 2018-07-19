<?php
/* @var $this PatientMergeRequestController */
/* @var $data_provider CActiveDataProvider */

?>

<?php $form = $this->beginWidget('CActiveForm', array(
    'id' => 'merge-form',
    'enableAjaxValidation' => false,
    'enableClientValidation' => true,

)); ?>


<div id="patientMergeWrapper" class="container content ">
  <form id="patientMergeList">
    <div class="cols-8 ">
        <?php $this->renderPartial('//base/_messages') ?>
      <section class="main-event view">
        <h2 class="event-title">Merge Requests</h2>
        <div class="filter panel">
          <div class="data-group">
            <div class="cols-10 column">
              <label>
                <input type="hidden" id="PatientMergeRequest_show_merged_hidden"
                       name="PatientMergeRequestFilter[show_merged]" value="0">
                <input type="checkbox" id="PatientMergeRequest_show_merged"
                       name="PatientMergeRequestFilter[show_merged]"
                       value="1" <?php echo $filters['show_merged'] && $filters['show_merged'] == 1 ? 'checked' : '' ?> >
                Show merged
              </label>
            </div>
            <div class="cols-2 column text-right">
              <img class="loader filter" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif') ?>"
                   alt="loading..." style="margin-right: 10px; display: none;"/>

              <button class="secondary small filter" type="button">Filter</button>
            </div>
          </div>
        </div>
        <div class="grid-view" id="inbox-table">
            <?php $this->renderPartial('//patientmergerequest/_list',
                array('data_provider' => $data_provider, 'filters' => $filters)) ?>
        </div>
      </section>
    </div>
  </form>
</div>

<?php $this->endWidget(); ?>


