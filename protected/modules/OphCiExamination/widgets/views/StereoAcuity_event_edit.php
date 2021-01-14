<?php

/**
 * @var \OEModule\OphCiExamination\models\StereoAcuity $element
 * @var \OEModule\OphCiExamination\widgets\StereoAcuity $this
 */
?>
<script type="text/javascript" src="<?= $this->getJsPublishedPath("StereoAcuity.js") ?>"></script>
<?php $model_name = CHtml::modelName($element); ?>
<div class="element-fields flex-layout full-width" id="<?= $model_name ?>_form">
    <input type="hidden" name="<?= $model_name ?>[present]" value="1"/> <!-- forces element validation -->
    <table class="cols-10 last-left">
        <colgroup>
            <col class="cols-3">
            <col class="cols-3">
            <col class="cols-3">
            <col class="cols-2">
            <col class="cols-1">
        </colgroup>
        <thead>
            <th><?= $this->getReadingAttributeLabel('method_id') ?></th>
            <th><?= $this->getReadingAttributeLabel('result') ?></th>
            <th><?= $this->getReadingAttributeLabel('correctiontype_id') ?></th>
            <th><?= $this->getReadingAttributeLabel('with_head_posture') ?></th>
            <th>&nbsp;</th>
        </thead>
        <tbody>
            <?= $this->renderEntriesForElement($element->entries) ?>
        </tbody>
    </table>
    <div class="add-data-actions flex-item-bottom " id="add-stereoacuity-popup">
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
        new OpenEyes.OphCiExamination.StereoAcuityController({
            container: document.querySelector('#<?= $model_name ?>_form')
        });
    });
</script>