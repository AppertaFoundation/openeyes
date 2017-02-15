<?php
/* @var $this PatientController */
/* @var $model Patient */
?>
<h1 class="badge">Patient</h1>
<div class="box content admin-content">
  <div class="large-10 column content admin large-centered">

    <div class="box admin">
      <h1 class="text-center">Create Patient</h1>

        <?php $this->renderPartial('crud/_form', array(
            'patient' => $patient,
            'contact' => $contact,
            'address' => $address,
        )); ?>
    </div>
  </div>
</div>