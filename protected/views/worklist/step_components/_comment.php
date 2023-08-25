<?php
/**
 * @var $partial int
 * @var $visit WorklistPatient
 * @var $model PathwayStep|PathwayTypeStep|Pathway
 */
?>
<div class="step-comments">
    <?php $comment = $model && !($model instanceof PathwayTypeStep) ? $model->comment : null;
    if (!$partial && $visit) { ?>
    <div class="flex js-comments-edit" style="<?= $comment ? 'display: none;' : '' ?>">
        <div class="cols-11">
            <input class="cols-full js-step-comments" type="text" maxlength="80" placeholder="Comments"
            <?= $comment ? 'value="'.$comment->comment.'"' : '' ?> data-test="pathway-comment-text-input" />
            <div class="character-counter">
                <span class="percent-bar"
                        style="width: <?= $comment ? strlen($comment->comment)/0.8 : 0 ?>%;"></span>
            </div>
        </div>
        <i class="oe-i save-plus js-save" data-test="save-pathway-comment-btn"></i>
    </div>
    <?php } ?>
    <div class="flex js-comments-view" style="<?= !$comment ? 'display: none;' : '' ?>">
        <div class="cols-11">
            <i class="oe-i comments small pad-right no-click"></i>
                <em class="comment"><?= $comment ? $comment->comment : '' ?></em>
        </div>
        <?php if (!$partial && $model && !($model instanceof PathwayTypeStep) && $visit->pathway && in_array((int)$visit->pathway->status, Pathway::inProgressStatuses(), true)) { ?>
            <i class="oe-i medium-icon pencil js-edit" data-test="edit-pathway-comment-btn"></i>
        <?php } ?>
    </div>
</div>
