<?php $answerResults = $element->catProm5AnswerResults; ?>

<div class="element-data full-width cols-10">
  <div>
    <table class="cols-full large-text">
      <colgroup>
        <col class="cols-6">
      </colgroup>
      <tbody>
        <?php foreach ($answerResults as $answerResult_item) {
            $ques = $answerResult_item->question;
            $answer = $answerResult_item->answer;
            ?>
        <tr>
          <td>
            <?php echo $ques->id.'.'. $ques->question; ?>
          </td>
          <td>
            <span><?= $answer->answer ?></span>
          </td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>
