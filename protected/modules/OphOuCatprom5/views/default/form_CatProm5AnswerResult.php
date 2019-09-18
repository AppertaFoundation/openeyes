
<?php $answerResults = $element->catProm5AnswerResults; ?>

<table class="cols-full">
  <tbody>
  <?php foreach ($answerResults as $answerResult_item) {
    $ques = $answerResult_item->question;
    $answers = $ques->answers;
    ?>
    <tr>
      <td>
        <?php echo $ques->id.' . '. $ques->question; ?>
      </td>
    </tr>
    <tr>
      <td>
        <input id="CatProm5EventResult<?= $ques->id?>" value="<?= $ques->id ?>"
               type="hidden" name="CatProm5EventResult[catProm5AnswerResults][<?=$ques->id?>][question_id]">
        <fieldset>
          <?php  foreach ($answers as $answer_item ){ ?>
            <label class="inline highlight cols-full">
            <?php if (isset($answerResult_item->answer_id)&&$answer_item->id == $answerResult_item->answer_id) {?>
              <input id="CatProm5AnswerResult_<?= $answer_item->id?>" value="<?= $answer_item->id ?>"
                     type="hidden" name="CatProm5EventResult[catProm5AnswerResults][<?=$ques->id?>][answer_id]">
              <input id="CatProm5AnswerResult_<?= $answer_item->id?>" value="<?= $answer_item->score ?>"
                     type="radio" name="CatProm5AnswerResult[<?=$ques->id?>]" checked>
            <?php } else { ?>
              <input id="CatProm5AnswerResult_<?= $answer_item->id?>" value="<?= $answer_item->id ?>"
                     type="hidden" name="CatProm5EventResult[catProm5AnswerResults][<?=$ques->id?>][answer_id]">
              <input id="CatProm5AnswerResult_<?= $answer_item->id?>" value="<?= $answer_item->score ?>"
                     type="radio" name="CatProm5AnswerResult[<?=$ques->id?>]">
            <?php } ?>
              <span><?= $answer_item->answer ?></span>
            </label>
          <?php } ?>
        </fieldset>
        <hr class="divider">
      </td>
    </tr>
  <?php } ?>
  </tbody>
</table>