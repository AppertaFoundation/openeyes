<?php
    $model_name = \CHtml::modelName($element) . "[entries][$row_count]";
    $comment_field_id = "safeguarding-entry-comment-field-$row_count";
    $comment_container_id = "safeguarding-entry-comment-container-$row_count";
    $comment_button_id = "safeguarding-entry-comment-button-$row_count";

    $has_comment = !empty($entry['comment']);

    $comment_button_style = "";
    $comment_container_style = "display: none;";

if ($has_comment) {
    $comment_button_style = "display: none;";
    $comment_container_style = "";
}
?>

<tr>
    <?= CHtml::hiddenField($model_name . '[id]', $entry->id, array('class' => 'safeguarding-entry-id')) ?>
    <?= CHtml::hiddenField($model_name . '[concern_id]', $entry->concern_id) ?>
    <td><?= $entry->concern->term ?></td>
    <td>
        <?php if ($editable) { ?>
        <div class="cols-full">
            <button id="<?= $comment_button_id ?>" type="button" class="button js-add-comments" style="<?= $comment_button_style ?>" data-comment-container="#<?= $comment_container_id?>">
                <i class="oe-i comments small-icon"></i>
            </button> <!-- comments wrapper -->
            <div class="cols-full comment-container" style="display: block;">
                <!-- comment-group, textarea + icon -->
                <div id="<?= $comment_container_id ?>" class="flex-layout flex-left js-comment-container" style="<?= $comment_container_style ?>" data-comment-button="#<?=$comment_button_id?>">
                    <?=
                        CHtml::textArea(
                            $model_name . '[comment]',
                            $entry->comment,
                            array(
                                'id' => $comment_field_id,
                                'autocomplete' => 'off',
                                'rows' => '1',
                                'class' => 'js-comment-field cols-full'
                            )
                        )
                    ?>
                    <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
                </div>
            </div>
        </div>
        <?php } elseif ($has_comment) { ?>
        <i class="oe-i comments-who small pad-right js-has-tooltip"
           data-tt-type="basic"
           data-tooltip-content="<small>User comment by </small><br/><?=User::model()->findByPk($entry['created_user_id'])->getFullName();?>">
        </i>
        <span class="user-comment"><?=$entry['comment']?></span>
        <?php } ?>
    </td>
    <td>
        <?php if ($editable) { ?>
        <i id="safeguarding-row-trash-<?=$row_count?>" data-row="<?=$row_count?>" class="oe-i safeguarding-trash trash"></i>
        <?php } ?>
    </td>
</tr>