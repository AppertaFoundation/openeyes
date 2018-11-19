<?php
/* @var $this PracticeController */
/* @var $model Practice */

$this->pageTitle = 'View Practice';
?>

<h1 class="badge">Practice Summary</h1>
<div class="row data-row">
  <div class="large-8 column">
    <div class="row data-row">
      <section class="box patient-info patient-contacts js-toggle-container">
        <h3 class="box-title">Contact Information:</h3>
        <a href="#" class="toggle-trigger toggle-hide js-toggle">
            <span class="icon-showhide">
                Show/hide this section
            </span>
        </a>
        <div class="js-toggle-body">
           <div class="row data-row">
                <div class="large-3 column">
                    <?php echo CHtml::label('Practice Contact', null); ?>
                </div>
                <div class="large-3 column end">
                    <div
                      class="data-value"><?php echo CHtml::encode($model->contact->getFullName()); ?>
                    </div>
                </div>
          </div>
          </div>
          <div class="row data-row">
            <div class="large-3 column">
                <?php echo CHtml::label('Practice Address', null); ?>
            </div>
            <div class="large-4 column end">
              <div class="data-value"><?php echo CHtml::encode($model->getAddressLines()); ?></div>
            </div>
          </div>
          <div class="row data-row">
              <div class="large-3 column">
                  <?php echo CHtml::label('Code', null); ?>
              </div>
              <div class="large-3 column end">
                  <div
                    class="data-value"><?php echo CHtml::encode($model->code); ?>
                  </div>
              </div>
          </div>
          <div class="row data-row">
            <div class="large-3 column">
                <?php echo CHtml::activeLabelEx($model->contact, 'primary_phone'); ?>
            </div>
            <div class="large-3 column end">
              <div
                  class="data-value"><?php echo isset($model->contact->primary_phone) ? CHtml::encode($model->contact->primary_phone) : 'Unknown'; ?></div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
    <?php if (Yii::app()->user->checkAccess('TaskCreatePractice')): ?>
      <div class="large-4 column end">
        <div class="box generic">
          <div class="row">
            <div class="large-12 column end">
              <p><?php echo CHtml::link('Update Practice Details',
                      $this->createUrl('/practice/update', array('id' => $model->id))); ?></p>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
</div>
