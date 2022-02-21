<?php
$answerResults = $eur->eurAnswerResults ? $eur->eurAnswerResults : $answerResults;
if ($eur->result == 0) {
    $msg = 'The patient is not suitable for Cataract Surgery.';
    $style_class = 'warning';
} elseif ($eur->result == 1) {
    $msg = 'The patient is suitable for Cataract Surgery.';
    $style_class = 'success';
}
if ($eur->eye_num == 1) {
    $eye_no = '1st Eye';
} elseif ($eur->eye_num == 2) {
    $eye_no = '2nd Eye';
}
?>
<section class="element full">
    <header class="element-header">
        <h3 class="element-title">Effective use of resources (EUR)</h3>
    </header>
    <div class="element-data full-width">
        <div class="flex-layout flex-top col-gap data-group">
            <table class="cols-full large-text">
                <tbody>
                    <tr>
                        <td>Which Eye?</td>
                        <td><?=$eye_no?></td>
                        <td></td>
                    </tr>
                    <?php foreach ($answerResults as $answerResult_item) {
                        $ques = $answerResult_item->question;
                        $answer = $answerResult_item->answer;
                        ?>

                    <tr>
                        <td class="cols-1">Statement <?=$ques->id ?></td>
                        <td class="cols-9">
                            <?=$ques->question ?>
                        </td>
                        <td class="cols-2">
                            <span><?= $answer->answer ?></span>
                        </td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td>Result</td>
                        <td>
                            <div class="flex-layout">
                                <div id="eur-res-ctn" class="flex-layout flex-left alert-box <?=$style_class?>">
                                    <div class="msg"><?=$msg?></div>
                                </div>
                            </div>
                        </td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>