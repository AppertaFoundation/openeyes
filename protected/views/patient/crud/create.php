<?php
/**
 * @var PatientController  $this
 * @var Patient $model
 * @var Contact $contact
 * @var PatientIdentifier[] $patient_identifiers
 */
?>
<div class="oe-full-header flex-layout">
  <div class="title wordcaps">Add&nbsp;<b>New Patient</b></div>
</div>
<?php $this->renderPartial('crud/_form', array(
    'patient' => $patient,
    'contact' => $contact,
    'address' => $address,
    'referral' => $referral,
    'patientuserreferral' => $patientuserreferral,
    'patient_identifiers' => $patient_identifiers,
)); ?>
