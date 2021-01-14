<?php

/**
 * @var \OEModule\OphCiExamination\models\ContrastSensitivity $element
 * @var \OEModule\OphCiExamination\widgets\ContrastSensitivity $this
 */
?>
<script type="text/javascript" src="<?= $this->getJsPublishedPath("ContrastSensitivity.js") ?>"></script>
<?php $model_name = CHtml::modelName($element); ?>
<div class="element-fields flex-layout full-width" id="<?= $model_name ?>_form">
    <input type="hidden" name="<?= $model_name ?>[present]" value="1"/> <!-- forces element validation -->
    <div class="cols-11">
        <table class="cols-full last-left" id="<?= $model_name ?>_entry_table">
            <colgroup>
                <col class="cols-3">
                <col class="cols-2">
                <col class="cols-2">
                <col class="cols-3">
            </colgroup>
            <thead>
            <th><?= $this->getResultAttributeLabel('contrastsensitivity_type_id') ?></th>
            <th><?= $this->getResultAttributeLabel('value') ?></th>
            <th><?= $this->getResultAttributeLabel('eye_id') ?></th>
            <th><?= $this->getResultAttributeLabel('correctiontype_id') ?></th>
            </thead>
            <tbody>
                <?= $this->renderEntriesForElement($element->results) ?>
            </tbody>
        </table>
        <div id="contrastsensitivity-comments" class="cols-full js-comment-container"
            data-comment-button="#add-contrastsensitivity-popup .js-add-comments"
            <?php
            if (!$element->comments) {
                echo 'style="display: none"';
            }
            ?>
        >
            <!-- comment-group, textarea + icon -->
            <div class="comment-group flex-layout flex-left">
                <textarea id="<?= $model_name ?>_comments"
                          name="<?= $model_name ?>[comments]"
                          class="js-comment-field cols-10"
                          placeholder="Enter comments here"
                          autocomplete="off" rows="1"
                          style="overflow: hidden; word-wrap: break-word; height: 24px;"><?= CHtml::encode($element->comments) ?></textarea>
                <i class="oe-i remove-circle small-icon pad-left  js-remove-add-comments"></i>
            </div>
        </div>
    </div>
    <div class="add-data-actions flex-item-bottom " id="add-contrastsensitivity-popup">
        <button class="button js-add-comments"
                type="button"
                data-comment-container="#<?= $model_name ?>_form .js-comment-container"
                <?php if ($element->comments) {
                    echo 'style="display: none"';
                }
                ?>
        >
            <i class="oe-i comments small-icon "></i>
        </button>
        <button class="button hint green js-add-select-search" data-adder-trigger="true" type="button">
            <i class="oe-i plus pro-theme"></i>
        </button>
    </div>
    <template class="hidden" data-entry-template="true">
        <?= $this->renderEntryTemplate() ?>
    </template>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        new OpenEyes.OphCiExamination.ContrastSensitivityController({
            container: document.querySelector('#<?= $model_name ?>_form')
        });
    });
</script>