<?php $questions = CatProm5Questions::model()->findAll(array('order'=>'display_order')); ?>

  <table class="cols-full">
    <tbody>
    <?php foreach ($questions as $ques) {
      $answers = $ques->answers;
      ?>
          <tr>
            <td>
              <?php echo $ques->id.'.'. $ques->question; ?>
            </td>
          </tr>
          <tr data-questionid="<?= $ques->id; ?>">
            <td>
              <fieldset>
                <?php  foreach ($answers as $answer_item ){ ?>
                  <label class="inline highlight cols-full">
                    <input id="CatProm5EventResult_<?= $answer_item->id?>" value="<?= $answer_item->score ?>"
                           type="radio" name="CatProm5EventResult[<?=$ques->id?>]">
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
