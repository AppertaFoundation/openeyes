<?php
/**
 * @var PatientController $this
 * @var Patient $model
 * @var Contact $contact
 * @var ArchivePatientIdentifier[] $patient_identifiers
 */
?>
<div class="oe-full-header flex-layout">
  <div class="title wordcaps">Update&nbsp;<strong><?= $patient->getFullName() ?></strong></div>
</div>
<?php $this->renderPartial('crud/_form', array(
    'patient' => $patient,
    'contact' => $contact,
    'address' => $address,
    'referral' => $referral,
    'patientuserreferral' => $patientuserreferral,
    'patient_identifiers' => $patient_identifiers,
    'pid_type_necessity_values' => $pid_type_necessity_values,
    'prevUrl' => $prevUrl,
    'is_update' => true,
)); ?>
