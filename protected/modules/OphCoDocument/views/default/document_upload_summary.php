<table class="cols-full">
    <tbody>
    <tr>
        <td>
            <ul class="dot-list large-text">
                <li><?= ucfirst($side); ?></li>
                <li class="js-document-name"><?= $document ? $document->name : ''; ?></li>
                <li class="js-document-size"><?= $document ? number_format($document->size / 1048576, 2) . 'Mb' : ''; ?></li>
            </ul>
        </td>
        <td>
            <i class="oe-i trash js-remove-document-action" data-side="<?=$side?>"></i>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <div class="flex-t">
                <!-- only show Annotated by information if the document is being updated -->
                <div>
                    <?php if ($this->is_updating) {
                        $user_name = $element->usermodified->first_name . ' ' . $element->usermodified->last_name;
                        $date = DateTime::createFromFormat('Y-m-d H:i:s', $element->last_modified_date);
                        echo $user_name . ' on ' . $date->format('j M Y');
                    } ?>
                </div>
                <div>
                    <button class="green hint js-annotate-image-action" <?= $document_id ? '' : 'style="display:none"'; ?>>Annotate</button>
                    <button class="blue hint js-download-image-action" <?= $document_id ? '' : 'style="display:none"'; ?>>Download</button>
                    <button class="green hint js-save-annotation-action" style="display: none;">Save annotation</button>
                    <button class="red hint js-cancel-annotation-action" style="display: none;">Cancel annotation</button>
                    <button id="document_<?= $side ?>_comment_button"
                            class="button js-add-comments"
                            data-comment-container="#document-<?= $side ?>-comments"
                            type="button"
                            data-hide-method="display"
                            style="display: <?= $element->{$side."_comment"} || array_key_exists("{$side}_comment", $element->getErrors()) ? 'none;' : '' ?>">
                        <i class="oe-i comments small-icon"></i>
                    </button>
                </div>
            </div>
        </td>
    </tr>
    </tbody>
</table>
<div class="js-comment-container flex-layout flex-left" id="document-<?= $side ?>-comments"
    <?= $element->{$side."_comment"} || array_key_exists("{$side}_comment", $element->getErrors()) ? '' : 'style="display:none;"' ?>
     data-comment-button="#document_<?= $side ?>_comment_button">
    <?= $form->textArea(
        $element,
        "{$side}_comment",
        array('rows' => '1', 'nowrapper' => true),
        false,
        ['placeholder' => 'Comments', 'class' => 'js-comment-field autosize cols-full']
    ); ?>
    <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
</div>