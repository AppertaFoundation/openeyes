<?php

/**
 * @var \OEModule\OphCiExamination\models\PrismReflex $element
 * @var \OEModule\OphCiExamination\widgets\PrismReflex $this
 */
?>
<?php $model_name = CHtml::modelName($element); ?>
<div class="element-fields flex-layout full-width" id="<?= $model_name ?>_form">
    <input type="hidden" name="<?= $model_name ?>[present]" value="1"/> <!-- forces element validation -->
    <div class="cols-11">
        <table class="cols-full last-left" id="<?= $model_name ?>_entry_table">
            <colgroup>
                <col class="cols-2">
                <col class="cols-2">
                <col class="cols-2">
                <col class="cols-2">
                <col class="cols-1">
            </colgroup>
            <thead>
                <th><?= $this->getReadingAttributeLabel('prismdioptre_id') ?></th>
                <th><?= $this->getReadingAttributeLabel('correctiontype_id') ?></th>
                <th><?= $this->getReadingAttributeLabel('prismbase_id') ?></th>
                <th><?= $this->getReadingAttributeLabel('finding_id') ?></th>
                <th><?= $this->getReadingAttributeLabel('with_head_posture') ?></th>
            </thead>
            <tbody>
                <?= $this->renderEntriesForElement($element->entries) ?>
            </tbody>
        </table>
        <div id="PFR-comments" class="cols-full js-comment-container"
             data-comment-button="#add-prismreflex-popup .js-add-comments"
             style="display: <?php if (!$element->comments) {
                    echo 'none';
                             }?>">
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
    <div class="add-data-actions flex-item-bottom " id="add-prismreflex-popup">
        <button class="button js-add-comments"
                type="button"
                data-comment-container="#<?= $model_name ?>_form .js-comment-container"
                style="<?= $element->comments ? "display: none" : "" ?>">
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
        new OpenEyes.UI.ElementController.MultiRow({
            container: document.querySelector('#<?= $model_name ?>_form')
        });
    });
</script>