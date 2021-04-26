<?php

/**
 * @var \OEModule\OphCiExamination\models\ConvergenceAccommodation $element
 * @var \OEModule\OphCiExamination\widgets\ConvergenceAccommodation $this
 */
?>
<?php $model_name = CHtml::modelName($element); ?>
<div class="element-fields flex-layout full-width" id="<?= $model_name ?>_form">
    <table class="cols-10 last-left">
        <colgroup>
            <col class="cols-2">
            <col class="cols-2">
            <col class="cols-6">
        </colgroup>
        <thead>
        <th><?= $element->getAttributeLabel('correctiontype_id') ?></th>
        <th><?= $element->getAttributeLabel('with_head_posture') ?></th>
        <th></th>
        </thead>
        <tbody>
        <tr>
            <td>
                <?= $form->dropDownList($element, 'correctiontype_id',
                    CHtml::listData($element->correctiontype_options, 'id', 'name'), [
                        'empty' => '- Select -',
                        'nowrapper' => true,
                        'data-adder-header' => $element->getAttributeLabel('correctiontype_id')
                    ]); ?>
            </td>
            <td>
                <?= $form->dropDownList($element, 'with_head_posture',
                    CHtml::listData($element->with_head_posture_options, 'id', 'name'), [
                        'empty' => '- Select -',
                        'nowrapper' => true,
                        'data-adder-header' => $element->getAttributeLabel('with_head_posture')
                    ]); ?>
            </td>
            <td>
                <?=\CHtml::activeTextArea($element, 'comments',
                    array(
                        'rows' => 1,
                        'placeholder' => $element->getAttributeLabel('comments'),
                        'class' => 'cols-full',
                        'style' => 'overflow-wrap: break-word; height: 24px;',
                    )) ?>
            </td>
        </tr>
        </tbody>
    </table>
    <div class="add-data-actions flex-item-bottom " id="add-convergenceaccommodation-popup">
        <button class="button hint green js-add-select-search" data-adder-trigger="true" type="button">
            <i class="oe-i plus pro-theme"></i>
        </button>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        new OpenEyes.UI.ElementController({
            container: document.querySelector('#<?= $model_name ?>_form')
        });
    });
</script>