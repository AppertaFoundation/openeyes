<div class="data-group">
  <div class="cols-2 column">
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
<?php if (isset($extras) && $extras) : ?>
<div class="data-group">
    <div class="cols-2 column">
        <label for="patient-search">Maiden Name</label>
    </div>
    <div class="cols-5 column end">
        <input type="text" id="patient-lookup-extra-maidenname" value="<?= ($model->patient->contact->maiden_name) ? $model->patient->contact->maiden_name : '' ?>" readonly>
    </div>
</div>
<div class="data-group">
  <div class="cols-2 column">
    <label for="patient-search">Date of Birth</label>
  </div>
  <div class="cols-5 column end">
    <input type="text" id="patient-lookup-extra-dob" value="<?= ($model->patient) ? $model->patient->dob : '' ?>" readonly>
  </div>
</div>
<div class="data-group">
  <div class="cols-2 column">
    <label for="patient-search">Hospital Number</label>
  </div>
  <div class="cols-5 column end">
    <input type="text" id="patient-lookup-extra-hos-num" value="<?= ($model->patient) ? $model->patient->hos_num : '' ?>" readonly>
  </div>
</div>
<?php endif;?>
<script type="text/javascript">
</script>
