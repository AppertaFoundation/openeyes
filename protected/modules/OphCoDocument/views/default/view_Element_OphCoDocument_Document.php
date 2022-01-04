<div class="element-data">
    <div class="cols-12 column">
        <div class="data-value "><b><?php if ($element->sub_type) {
            echo $element->sub_type->name;
                                    } ?></b></div>
    </div>
    <?php
    if ($element->single_document) { ?>
        <div class="cols-12 column">
            <?php
            $this->renderPartial('view_' . $this->getTemplateForMimeType($element->single_document->mimetype), array('element' => $element, 'index' => 'single_document'));
            ?>
        </div>
    <?php } ?>
    <?php if ($element->single_comment) : ?>
        <div class="element-fields flex-layout flex-top col-gap">
            <div class="cols-2 column">
                <?= $element->getAttributeLabel('single_comment') ?>
            </div>
            <div class="cols-10 column">
                <?= nl2br($element->single_comment); ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php if ($element->right_document_id || $element->left_document_id) { ?>
<div id="ophco-document-viewer">
    Jump to:
    <?php if ($element->right_document_id) { ?>
        <a href="#right-eye">Right eye</a> |
    <?php } ?>

    <?php if ($element->left_document_id) { ?>
        <a href="#left-eye">Left eye</a>
    <?php } ?>
    <hr>
<?php } ?>
    <?php if ($element->right_document_id || $element->right_comment || $element->left_document_id || $element->left_comment) { ?>
        <?php if ($element->right_document_id) {
            ?>
        <div id="right-eye">
            <h2>Right eye</h2>
            <?php
            $this->renderPartial('view_' . $this->getTemplateForMimeType($element->right_document->mimetype), array('element' => $element, 'index' => 'right_document'));
            ?>
        </div>
        <?php } ?>
        <?php if ($element->right_comment) : ?>
        <div class="element-fields flex-layout flex-top col-gap">
            <div class="cols-2 column">
                <?= $element->getAttributeLabel('right_comment') ?>
            </div>
            <div class="cols-10 column">
                <?= nl2br($element->right_comment); ?>
            </div>
        </div>
        <?php endif; ?>
    <hr>
        <?php if ($element->left_document_id) { ?>
        <div id="left-eye">
            <h2>Left eye</h2>
            <?php
            $this->renderPartial('view_' . $this->getTemplateForMimeType($element->left_document->mimetype), array('element' => $element, 'index' => 'left_document'));
            ?>
        </div>
        <?php } ?>
        <?php if ($element->left_comment) : ?>
        <div class="element-fields flex-layout flex-top col-gap">
            <div class="cols-2 column">
                <?= $element->getAttributeLabel('left_comment') ?>
            </div>
            <div class="cols-10 column">
                <?= nl2br($element->left_comment); ?>
            </div>
        </div>
        <?php endif; ?>
</div>
    <?php } ?>