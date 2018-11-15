<?php
/* @var $this GpController */
/* @var $model Gp */
/* @var $context String */
$this->pageTitle = 'Update Practitioner';
?>

<div>
    <div class="oe-full-header flex-layout">
        <div class="title wordcaps">
            <b>Practitioner</b>
        </div>

    </div>
    <div class="oe-full-content oe-new-patient flex-layout flex-top">
        <div class="patient-content">
            <?php $this->renderPartial('_form', array('model' => $model, 'context' => null)); ?>
        </div>
    </div>
</div>
<!--<h1 class="badge">Practitioner</h1>-->
<!--<div class="box content admin-content">-->
<!--  <div class="large-10 column content admin large-centered">-->
<!--    <div class="box admin">-->
<!--      <h1 class="text-center">Update Practitioner Details</h1>-->
<!--        --><?php //$this->renderPartial('_form', array('model' => $model, 'context' => null)); ?>
<!--    </div>-->
<!--  </div>-->
<!--</div>-->
