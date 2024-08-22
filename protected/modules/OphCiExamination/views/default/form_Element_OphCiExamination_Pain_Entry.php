<?php
    $model_name = CHtml::modelName($element) . "[entries][$row_count]";
    $datetime = new DateTime($model->datetime);
?>

<tr data-row-number="<?=$row_count?>">
    <?= isset($model->id) ? CHtml::hiddenField($model_name . '[id]', $model->id, array('class' => 'pain-entry-id')) : '' ?>
    <td>
        <i class="oe-i happy medium no-click"></i>
    </td>
    <?php
        $pain_score = $model->pain_score;

        $pain_color = 'good';

    if ($pain_score <= 3) {
        $pain_color = 'good';
    } elseif ($pain_score <= 7) {
        $pain_color = 'issue';
    } else {
        $pain_color = 'warning';
    }

    for ($i = 0; $i <= 10; $i++) {
        echo "<td class=\"center\">";

        //needs type coercion as pain_score may be a string at this point
        if ($pain_score == $i) {
            echo CHtml::hiddenField($model_name . '[pain_score]', $pain_score);
            $id = "id='pain-entry-row-$row_count-score-$pain_score'";
            echo "<span $id class=\"highlighter $pain_color\">$i</span>";
        } else {
            echo '<span class="dot-list-divider fade"></span>';
        }
        echo '</td>';
    }
    ?>
    <td>
        <i class="oe-i crying medium no-click"></i>
    </td>
    <td>
        <?= CHtml::hiddenField($model_name . '[datetime]', $model->datetime) ?>
        <span class="oe-date">
            <span class="day"><?=$datetime->format('j')?></span>
            <span class="mth"><?=$datetime->format('M')?></span>
            <span class="yr"><?=$datetime->format('Y')?></span>
        </span>
    </td>
    <td class="nowrap">
        <small>at</small> <?=$datetime->format('H:i')?>
    </td>
    <td>
        <?php if (!empty($model->comment)) { ?>
            <i class="oe-i comments-who small pad-right js-has-tooltip"
               data-tt-type="basic"
               data-tooltip-content="<small>User comment by </small><br/><?= isset($model->created_user_id) ? User::model()->findByPk($model->created_user_id)->getFullName() : '';?>">
            </i>
        <?php } ?>
        <?= CHtml::hiddenField($model_name . '[comment]', $model->comment); ?>
        <span class="user-comment"><?=$model->comment?></span>
    </td>
    <td>
        <?php if ($editable) { ?>
            <i id="pain-trash-row-<?=$row_count?>" class="oe-i pain-trash trash"></i>
        <?php } ?>
    </td>
</tr>
