<?php
/* @var $this PracticeController */
/* @var $model Practice */
$this->pageTitle = 'Update Practice';
?>

<h1 class="badge">Practice</h1>
<div class="box content admin-content">
  <div class="large-10 column content admin large-centered">
    <div class="box admin">
      <h1 class="text-center">Update Practitioner Details</h1>
        <?php $this->renderPartial('_form', array('model' => $model,'address' => $address,'contact' => $contact)); ?>
    </div>
  </div>
</div>
