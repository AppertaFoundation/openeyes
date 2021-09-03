<?php

/**
 * @var \OEModule\OphCiExamination\models\Element_OphCiExamination_ColourVision $element
 * @var \OEModule\OphCiExamination\widgets\ColourVision $this
 */
?>
<?php $model_name = CHtml::modelName($element); ?>
<script type="text/javascript" src="<?= $this->getJsPublishedPath("ColourVision.js") ?>"></script>
<div class="element-fields eye-divider" id="<?= $model_name ?>_form">
    <div class="element-eyes">
        <?php echo $form->hiddenField($element, 'eye_id', array('class' => 'sideField')) ?>
        <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) { ?>
            <div id="<?= $model_name ?>_<?= $eye_side ?>_readings_form" class="js-element-eye <?= $eye_side ?>-eye column <?= $page_side ?> " data-side="<?= $eye_side ?>">
                <div class="active-form" style="<?= $element->hasEye($eye_side) ? '' : 'display: none;' ?>">
                    <div class="remove-side"><i class="oe-i remove-circle small"></i></div>
                    <table id="<?= $model_name ?>_<?= $eye_side ?>_readings" class="cols-full colourvision_table_<?= $eye_side ?>">
                        <colgroup>
                            <col class="cols-4">
                            <col class="cols-2">
                            <col class="cols-2">
                            <col class="cols-2">
                        </colgroup>
                        <thead>
                        <tr>
                            <th><?= $this->getReadingAttributeLabel('method_id') ?></th>
                            <th><?= $this->getReadingAttributeLabel('value_id') ?></th>
                            <th><?= $this->getReadingAttributeLabel('correctiontype_id') ?></th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody class="plain">
                            <?= $this->renderReadingsForElement($element->{"{$eye_side}_readings"}) ?>
                        </tbody>
                    </table>
                    <div class="flex-layout flex-right">
                        <div class="add-data-actions flex-item-bottom" id="<?= $eye_side ?>-add-colour_vision_reading">
                            <button class="button hint green" type="button" data-adder-trigger="true">
                                <i class="oe-i plus pro-theme"></i>
                            </button>
                        </div>
                    </div>

                    <template class="hidden" data-entry-template="true">
                        <?= $this->renderReadingTemplateForSide($eye_side) ?>
                    </template>
                </div>
                <div class="inactive-form" style="display: <?php if ($element->hasEye($eye_side)) {
                    ?> none <?php
                } ?>">
                    <div class="add-side">
                        <a href="#">Add <?= $eye_side ?> side <span class="icon-add-side"></span></a>
                    </div>
                </div>
            </div>
            <script type="text/javascript">
                $(document).ready(function () {
                    new OpenEyes.OphCiExamination.ColourVisionController({
                        container: document.querySelector('#<?= $model_name ?>_<?= $eye_side ?>_readings_form')
                    });
                });
            </script>
        <?php } ?>
    </div>
</div>
