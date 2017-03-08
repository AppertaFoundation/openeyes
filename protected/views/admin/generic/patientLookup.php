<div class="row field-row">
  <div class="large-2 column">
    <label for="patient-search">Patient Lookup</label>
  </div>
  <div class="large-5 column end">
      <span style="font-size: 12px;">Find a patient by Hospital Number, NHS Number, Firstname Surname or Surname, Firstname.</span>
    <input type="hidden" name="<?= get_class($model) ?>[patient_lookup_gender]"/>
    <input type="hidden" name="<?= get_class($model) ?>[patient_lookup_deceased]"/>
    <input type="text" name="search" id="patient-search" class="form panel search large ui-autocomplete-input" placeholder="Enter search..." autocomplete="off">
    <div style="display:none" class="no-result-patients warning alert-box">
      <div class="small-12 column text-center">
        No results found.
      </div>
    </div>
      <?php if (!$model->patient_id): ?>
        <div id="patient-result" style="display: none">

        </div>
        <input type="hidden" name="<?= get_class($model) ?>[patient_id]" id="patient-result-id">
      <?php else: ?>
        <div id="patient-result">
          <a href="/patient/view/<?= $model->patient->id ?>" title="Patient Record"> <?= $model->patient->fullName ?></a>
        </div>
        <input type="hidden" name="<?= get_class($model) ?>[patient_id]" id="patient-result-id" value="<?= $model->patient_id ?>">
      <?php endif; ?>
  </div>
  <div class="large-3 column text-left">
    <img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('/img/ajax-loader.gif') ?>" alt="loading..." style="margin-right: 10px; display: none;"/>
  </div>
</div>
<?php if(isset($extras) && $extras): ?>
<div class="row field-row">
  <div class="large-2 column">
    <label for="patient-search">Date of Birth</label>
  </div>
  <div class="large-5 column end">
    <input type="text" id="patient-lookup-extra-dob" value="<?= ($model->patient) ? $model->patient->dob : '' ?>" readonly>
  </div>
</div>
<div class="row field-row">
  <div class="large-2 column">
    <label for="patient-search">Hospital Number</label>
  </div>
  <div class="large-5 column end">
    <input type="text" id="patient-lookup-extra-hos-num" value="<?= ($model->patient) ? $model->patient->hos_num : '' ?>" readonly>
  </div>
</div>
<?php endif;?>
<script type="text/javascript">
  $(document).ready(function () {
    OpenEyes.UI.Search.init($('#patient-search'));
    OpenEyes.UI.Search.getElement().autocomplete('option', 'select', function (event, uid) {
      $('#patient-search').hide();
      $('#patient-result').html('<span><a href="/patient/view/' + uid.item.id + '" title="Patient Record">' + uid.item.first_name + ' ' + uid.item.last_name + '</a></span>').show();
      $('#patient-result-id').val(uid.item.id);
      $('input[name="<?=get_class($model)?>\[patient_lookup_gender\]"]').val(uid.item.gender).trigger('change');
      $('input[name="<?=get_class($model)?>\[patient_lookup_deceased\]"]').val(uid.item.is_deceased).trigger('change');
      $('#patient-lookup-extra-dob').val(uid.item.dob);
      $('#patient-lookup-extra-hos-num').val(uid.item.hos_num);
    });
  });
</script>
