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

                </div>
            </div>
        </td>
    </tr>
    </tbody>
</table>