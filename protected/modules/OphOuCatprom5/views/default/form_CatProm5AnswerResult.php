
<?php $questions = CatProm5Questions::model()->findAll(array('order'=>'display_order'));
      $answerResults = $element->catProm5AnswerResults;
?>

<div class="element-fields full-width flex-layout cols-10">
  <table>
    <tbody>
      <?php foreach ($questions as $ques) {
          $index = $ques->id - 1;
          $answers = $ques->answers;
          foreach ($answerResults as $anr_item) {
              if ($anr_item->question_id == $ques->id) {
                  $answerResult_item = $anr_item;
              }
          }
          ?>
      <tr>
        <td>
          <?php
          if($index !=0){ // if this isn't the first row, add a divider
            ?>
          <hr class="divider">
          <?php } ?>
          <?php echo $ques->id.' . '. $ques->question; ?>
        </td>
      </tr>
      <tr style='border-bottom: none;'><!--there doesn't need to be a border after here -->
        <td>
          <?php if (isset($answerResult_item)  && isset($answerResult_item->id)) { ?>
              <?=\CHtml::hiddenField( $name_stub .'['. $index .'][id]', @$answerResult_item->id)?>
          <?php } ?>
          <?php if (isset($element)  && $element->id) { ?>
              <?=\CHtml::hiddenField( $name_stub .'['. $index .'][element_id]', @$element->id)?>
          <?php } ?>
            <?=\CHtml::hiddenField( $name_stub .'['. $index .'][question_id]', $ques->id)?>

          <?php  foreach ($answers as $answer_item) {?>
              <label class="inline highlight cols-full">
                <input class="cat_prom5_answer_score"
                      id="CatProm5AnswerResult_<?= $answer_item->id?>"
                      value="<?= $answer_item->id ?>"
                      data-score="<?= $answer_item->score?>"
                      type="radio"
                      name="<?= $name_stub .'['. $index .'][answer_id]'?>"
                      data-question="<?=$ques->id?>"
                      required
                      <?= isset($answerResult_item->answer_id)&& @$answerResult_item->answer_id===$answer_item->id ?'checked':''?>
                >
                <span><?= $answer_item->answer ?></span>
              </label>
          <?php } ?>
          </fieldset>
        </td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
</div>