<?php
/* @var $this PatientMergeRequestController */
/* @var $model PatientMergeRequest */

?>

<div id="patientMergeWrapper" class="container content">

  <div class="cols-4 column large-centered text-right large-offset-8">
    <section class="box dashboard">
        <?php
        echo CHtml::link('Back to Patient Merge list', array('patientMergeRequest/index'),
                array('class' => 'button small')) . ' ';
        echo CHtml::link('add', array('patientMergeRequest/create'), array('class' => 'button small'));
        ?>
    </section>
  </div>

  <div class="cols-8 column large-centered">
      <?php $this->widget('zii.widgets.grid.CGridView', array(
          'itemsCssClass' => 'standard',
          'dataProvider' => $data_provider,
          'summaryText' => '<h3><small> {start}-{end} of {count} </small></h3>',
          'htmlOptions' => array('id' => 'patientMergeList'),
          'columns' => array('log'),
      )); ?>

      <?=\CHtml::activeTextArea($model, 'comment', array('disabled' => 'disabled')); ?>
    <br>
  </div>
