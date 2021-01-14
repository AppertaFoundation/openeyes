<?php

/**
 * @var \OEModule\OphCiExamination\models\SensoryFunction $element
 * @var \OEModule\OphCiExamination\widgets\SensoryFunction $this
 */
?>
<?php $model_name = CHtml::modelName($element); ?>
<div class="element-fields flex-layout full-width" id="<?= $model_name ?>_form">
    <input type="hidden" name="<?= $model_name ?>[present]" value="1" /> <!-- forces validation -->
    <table class="cols-10 last-left">
        <colgroup>
            <col class="cols-2">
            <col class="cols-2">
            <col class="cols-2">
            <col class="cols-3">
            <col class="cols-2">
            <col class="cols-1">
        </colgroup>
        <thead>
        <th><?= $this->getEntryAttributeLabel('entry_type_id') ?></th>
        <th><?= $this->getEntryAttributeLabel('distance_id') ?></th>
        <th><?= $this->getEntryAttributeLabel('correctiontypes') ?></th>
        <th><?= $this->getEntryAttributeLabel('result_id') ?></th>
        <th><?= $this->getEntryAttributeLabel('with_head_posture') ?></th>
        <th>&nbsp;</th>
        </thead>
        <tbody>
        <?= $this->renderEntriesForElement($element->entries) ?>
        </tbody>
    </table>
    <div class="add-data-actions flex-item-bottom " id="add-sensoryfunction-popup">
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