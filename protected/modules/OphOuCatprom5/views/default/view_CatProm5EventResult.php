<?php
Yii::log(var_export($element, true));
$this->renderPartial('view_CatProm5AnswerResult', array(
  'element'=>$element,
));
?>
</section>
<section class="element full">
  <header class="element-header">
    <!-- Add a element remove flag which is used when saving data -->
    <input type="hidden" name="[element_removed]CatProm5EventResult" value="0">
    <!-- Element title -->
    <h3 class="element-title">Questionare Score</h3>
  </header>
  <div class="element-data full-width">
    <div class="cols-11">
      <div class="flex-layout flex-right">
        <div class="highlighter large-text" id="idg-js-demo-score"><?= $element->total_raw_score ?></div>
      </div>
    </div><!-- cols -->
  </div>
