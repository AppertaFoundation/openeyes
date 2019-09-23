<?php
$name_stub = CHtml::modelName($element) . '[catProm5AnswerResults]';
  $this->renderPartial('form_CatProm5AnswerResult', array(
    'element'=>$element,
    'name_stub' => $name_stub
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
  <!-- Additional element title information -->
  <!-- Element actions -->
  <div class="element-actions">
    <!-- order is important for layout because of Flex -->
    <!-- remove MUST be last element -->
    <span class="disabled" title="This is a mandatory element and cannot be closed.">
          <i class="oe-i trash-blue disabled"></i>
        </span>
  </div>
<!-- Additional element title information -->
<!-- Element actions -->
<div class="element-actions">
  <!-- order is important for layout because of Flex -->
  <!-- remove MUST be last element -->
  <span class="disabled" title="This is a mandatory element and cannot be closed.">
          <i class="oe-i trash-blue disabled"></i>
        </span>
</div>
<div class="element-fields full-width flex-layout">
  <div class="cols-11">
    <div class="flex-layout">
        <?php echo $form->hiddenInput($element, 'total_raw_score', false, array())?>
      <div class="highlighter large-text" id="js_cat_prom5_total_score"><?= isset($element->total_raw_score)?$element->total_raw_score:0 ?></div>
    </div>
  </div><!-- cols -->
  <script type="text/javascript">
    document.addEventListener('click', function ( e ) {
      let score = 0;
      if( e.target.type == 'radio'){
        /*
        add up all the scores
        then update the score
        */
        let radios = $('.cat_prom5_answer_score');
        for (let i=0; i< radios.length; i++){
          if(radios[i].checked === true) {
            score+= parseInt(radios[i].dataset['score']);
          }
        }
        let elemScore = document.querySelector('#js_cat_prom5_total_score');
        elemScore.textContent = score;
        $('#CatProm5EventResult_total_raw_score').val(score);
      }
    });
  </script>
</div>
