<?php
$exam_api = Yii::app()->moduleAPI->get('OphCiExamination');
if($exam_api){
  $alpha = $exam_api->mostRecentCheckedAlpha($this->patient->id);
  switch ($alpha->has_risk){
    case '0' :
    $alpha_result =  'No (' . Helper::convertMySQL2NHS($alpha->element->event->event_date) . ')';
    break;
    case '1' :
    $alpha_result =  'Yes - ' . $alpha->comments . ' (' . Helper::convertMySQL2NHS($alpha->element->event->event_date) . ')';
    break;
    default:
    $alpha_result = "Not Checked";
  }

  $anti = $exam_api->mostRecentCheckedAnticoag($this->patient->id);
  switch ($anti->has_risk){
    case '0' :
    $anti_result =  'No (' . Helper::convertMySQL2NHS($anti->element->event->event_date) . ')';
    break;
    case '1' :
    $anti_result =  'Yes - ' . $anti->comments . ' (' . Helper::convertMySQL2NHS($anti->element->event->event_date) . ')';
    break;
    default:
    $anti_result = "Not Checked";
  }
}
?>


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
