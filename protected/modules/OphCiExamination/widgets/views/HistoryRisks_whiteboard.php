<?php
$alpha_result = $anti_result = "Unknown";
if ($alpha = $element->getRiskEntryByName('Alphablocker')) {
    $alpha_result = $alpha->getDisplayHasRisk() . ($alpha->comments ? ' - ' . $alpha->comments : '') . '(' . Helper::convertMySQL2NHS($alpha->element->event->event_date) . ')';
}
if ($anti = $element->getRiskEntryByName('Anticoagulant')) {
    $anti_result = $anti->getDisplayHasRisk() . ($alpha->comments ? ' - ' . $alpha->comments : '') . '(' . Helper::convertMySQL2NHS($alpha->element->event->event_date) . ')';
} ?>

<div class="mdl-cell mdl-cell--4-col">
  <div class="mdl-card mdl-shadow--2dp">
    <div class="mdl-card__title mdl-card--expand risk">
      <h2 class="mdl-card__title-text">Alpha-blockers</h2>
    </div>
    <div class="mdl-card__supporting-text">
        <?=$alpha_result?>
    </div>
  </div>
</div>
<div class="mdl-cell mdl-cell--4-col">
  <div class="mdl-card mdl-shadow--2dp">
    <div class="mdl-card__title mdl-card--expand risk">
      <h2 class="mdl-card__title-text">Anticoagulants</h2>
    </div>
    <div class="mdl-card__supporting-text">
        <?=$anti_result?> <br>
      INR: <?php
        $lab_result = Element_OphInLabResults_Inr::model()->findPatientResultByType($this->patient->id, '1');
        $inr = $lab_result ? $lab_result : 'None';
        echo $inr;
        ?>
    </div>
  </div>
</div>
