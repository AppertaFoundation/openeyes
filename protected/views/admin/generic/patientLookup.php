<div class="data-group flex-layout cols-full">
  <div class="cols-7 column">
    <label for="patient-search">Patient:</label>
  </div>
  <div class="cols-5 column end">
    <input type="hidden" name="<?= get_class($model) ?>[patient_lookup_gender]"/>
    <input type="hidden" name="<?= get_class($model) ?>[patient_lookup_deceased]"/>
        <div id="patient-result">
          <a href="/patient/view/<?= $model->patient->id ?>" title="Patient Record"> <?= $model->patient->fullName ?></a>
        </div>
        <input type="hidden" name="<?= get_class($model) ?>[patient_id]" id="patient-result-id" value="<?= $model->patient_id ?>">
  </div>
</div>
<?php if (isset($extras) && $extras) { ?>
    <?php
    $institution_id = Institution::model()->getCurrent()->id;
    $site_id = Yii::app()->session['selected_site_id'];
    ?>
<div class="data-group flex-layout cols-full">
    <div class="cols-7 column">
        <label for="patient-search">Maiden Name</label>
    </div>
    <div class="cols-5 column end">
        <input type="text" id="patient-lookup-extra-maidenname" value="<?= ($model->patient->contact->maiden_name) ? $model->patient->contact->maiden_name : '' ?>" readonly>
    </div>
</div>
<div class="data-group flex-layout cols-full">
  <div class="cols-7 column">
    <label for="patient-search">Date of Birth</label>
  </div>
  <div class="cols-5 column end">
    <input type="text" id="patient-lookup-extra-dob" value="<?= ($model->patient) ? $model->patient->dob : '' ?>" readonly>
  </div>
</div>
<div class="data-group flex-layout cols-full">
  <div class="cols-7 column">
    <label for="patient-search"><?= PatientIdentifierHelper::getIdentifierDefaultPromptForInstitution(SettingMetadata::model()->getSetting('display_primary_number_usage_code'), $institution_id, $site_id) ?></label>
  </div>
  <div class="cols-5 column end">
    <input type="text" id="patient-lookup-extra-hos-num" value="<?= ($model->patient) ? PatientIdentifierHelper::getIdentifierValue(PatientIdentifierHelper::getIdentifierForPatient(SettingMetadata::model()->getSetting('display_primary_number_usage_code'), $model->patient->id, $institution_id, $site_id)) : '' ?>" readonly>
  </div>
</div>
<?php } ?>
<script type="text/javascript">
</script>
