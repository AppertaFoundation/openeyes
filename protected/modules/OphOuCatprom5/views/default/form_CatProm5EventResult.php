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
    <h3 class="element-title">Questionnaire Score</h3>
  </header>
<div class="element-fields full-width cols-10">
  <div class="flex-layout">
    <div class="flex-layout flex-left">
        <?php echo $form->hiddenInput($element, 'total_raw_score', false, array())?>
        Raw Score  (Absolute): &nbsp;
      <div class="highlighter large-text" id="js_cat_prom5_total_score"><?= isset($element->total_raw_score)?$element->total_raw_score:0 ?></div>
    </div>
    <div class="flex-layout flex-right">
        <?php echo $form->hiddenInput($element, 'total_rasch_measure', false, array())?>
        Rasch Score : &nbsp;
        <div class="highlighter large-text" id="js_cat_prom5_rasch_measure"><?= isset($element->total_rasch_measure)?$element->total_rasch_measure:'Please answer questions 1-5' ?></div>
    </div>
  </div>
  <script type="text/javascript">
    document.addEventListener('change', function ( e ) {
      let score = 0;
      let ques = [0,0,0,0,0,0];
      let canCalcFlag = 1;      
      let rasch = 'Please answer questions 1-5';

      if( e.target.type == 'radio'){
        /*
        add up all the scores
        then update the score
        */
        let radios = $('.cat_prom5_answer_score');
        for (let i=0; i< radios.length; i++){
          if(radios[i].checked === true) {
            score+= parseInt(radios[i].dataset['score']);
            ques[radios[i].dataset['question'] -1]=1; //mark this questions as answered
          }
        }
        rasch = scoreToRasch(score); // calculate preview Rasch Score

        for(let q=0; q<5;q++){ //flag any unanswered questions
          if (!ques[q]){
            canCalcFlag =0;
          }
        }
        
        let elemScore = document.querySelector('#js_cat_prom5_total_score');
        let elemRasch = document.querySelector('#js_cat_prom5_rasch_measure');
        elemScore.textContent = score;
        if(canCalcFlag){
          
          elemRasch.textContent = rasch;
        }
        else{
          elemRasch.textContent = 'Please answer questions 1-5'
        }
        $('#CatProm5EventResult_total_raw_score').val(score); // Save the raw score
      }
    });

    function scoreToRasch(Score){
      let rasch = ""
      switch(Score){ // This is a hack to get a preview of the rash score before the event is saved. This value is not saved.
            case 0 : rasch = '-9.18'; break;
            case 1 : rasch = '-6.80'; break;
            case 2 : rasch = '-4.92'; break;
            case 3 : rasch = '-4.03'; break;
            case 4 : rasch = '-3.37'; break;
            case 5 : rasch = '-2.81'; break;
            case 6 : rasch = '-2.29'; break;
            case 7 : rasch = '-1.80'; break;
            case 8 : rasch = '-1.31'; break;
            case 9 : rasch = '-0.82'; break;
            case 10: rasch = '-0.32'; break;
            case 11: rasch = '0.18' ; break;
            case 12: rasch = '0.69' ; break;
            case 13: rasch = '1.22' ; break;
            case 14: rasch = '1.76' ; break;
            case 15: rasch = '2.33' ; break;
            case 16: rasch = '2.93' ; break;
            case 17: rasch = '3.56' ; break;
            case 18: rasch = '4.23' ; break;
            case 19: rasch = '4.98' ; break;
            case 20: rasch = '6.01' ; break;
            case 21: rasch = '7.45' ; break;           
          }
          return rasch;
    }
  </script>
</div>
