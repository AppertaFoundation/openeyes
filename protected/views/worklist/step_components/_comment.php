<div class="step-comments">
    <?php if (!$partial && $pathway) { ?>
    <div class="flex js-comments-edit" style="<?= !($model instanceof PathwayTypeStep) && $model->comment ? 'display: none;' : '' ?>">
        <div class="cols-11">
            <input class="cols-full js-step-comments" type="text" maxlength="80" placeholder="Comments"
            <?= !($model instanceof PathwayTypeStep) && $model->comment ? 'value="'.$model->comment->comment.'"' : '' ?>/>
            <div class="character-counter">
                <span class="percent-bar"
                        style="width: <?= $model->comment ? strlen($model->comment->comment)/0.8 : 0 ?>%;"></span>
            </div>
        </div>
        <i class="oe-i save-plus js-save"></i>
    </div>
    <?php } ?>
    <div class="flex js-comments-view" style="<?= !$model->comment ? 'display: none;' : '' ?>">
        <div class="cols-11">
            <i class="oe-i comments small pad-right no-click"></i>
                <em class="comment"><?= !($model instanceof PathwayTypeStep) && $model->comment ? $model->comment->comment : '' ?></em>
        </div>
        <?php if (!$partial && !($model instanceof PathwayTypeStep) && in_array((int)$pathway->status, Pathway::inProgressStatuses(), true)) { ?>
            <i class="oe-i medium-icon pencil js-edit"></i>
        <?php } ?>
    </div>
</div>