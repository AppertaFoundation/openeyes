<?php
/* @var $this GpController */
/* @var $model Gp */

$this->pageTitle = 'View Practitioner';
?>

<h1 class="badge">Practitioner Summary</h1>
<div class="row data-row">
  <div class="large-8 column">
    <section class="box patient-info js-toggle-container">
      <h3 class="box-title">Contact Information:</h3>
      <a href="#" class="toggle-trigger toggle-hide js-toggle">
            <span class="icon-showhide">
                Show/hide this section
            </span>
      </a>
      <div class="js-toggle-body">
        <div class="row data-row">
          <div class="large-3 column">
            <div class="data-label">Name:</div>
          </div>
          <div class="large-4 column end">
            <div class="data-value"><?php echo CHtml::encode($model->getCorrespondenceName()); ?></div>
          </div>
        </div>
        <div class="row data-row">
          <div class="large-3 column">
            <div class="data-label">Phone Number:</div>
          </div>
          <div class="large-3 column end">
            <div
                class="data-value"><?php echo isset($model->contact->primary_phone) ? CHtml::encode($model->contact->primary_phone) : 'Unknown'; ?></div>
          </div>
        </div>
        <div class="row data-row">
          <div class="large-3 column">
            <div class="data-label">Role:</div>
          </div>
          <div class="large-4 column end">
            <div class="data-value"><?php echo CHtml::encode(isset($model->contact->label)?$model->contact->label->name:''); ?></div>
          </div>
        </div>
        <!--Add the address row here when GPs get tied directly to practices rather than through patient records.-->
      </div>
    </section>
  </div>
    <?php if (Yii::app()->user->checkAccess('TaskCreateGp')): ?>
      <div class="large-4 column end">
        <div class="box generic">
          <div class="row">
            <div class="large-12 column end">
              <p><?php echo CHtml::link('Update Practitioner Details',
                      $this->createUrl('/gp/update', array('id' => $model->id))); ?></p>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
</div>
