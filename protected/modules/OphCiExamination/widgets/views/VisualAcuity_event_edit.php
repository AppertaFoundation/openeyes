<?php

/**
 * @var \OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity $element
 * @var \OEModule\OphCiExamination\widgets\VisualAcuity $this
 */

$model_name = CHtml::modelName($element);
$test_prefix = $this->isForNear() ? 'near-visual-acuity' : 'visual-acuity';
?>
<script type="text/javascript" src="<?= $this->getJsPublishedPath("VisualAcuity.js") ?>"></script>
<script type="text/javascript">
    OpenEyes.OphCiExamination.VisualAcuityUnitOptions = OpenEyes.OphCiExamination.VisualAcuityUnitOptions || {};
    OpenEyes.OphCiExamination.VisualAcuityUnitOptions["<?= $model_name ?>"] = <?= $this->getJsonUnitOptions($element) ?>;
</script>
<?php $this->beginClip('element-header-additional'); ?>
    <button class="va-change-complexity change-complexity"
        data-element-type-class="<?= $model_name ?>"
        data-record-mode="<?= $element::RECORD_MODE_SIMPLE ?>"
        data-eye-id="<?= $element::LEFT | $element::RIGHT ?>"
    >Simple inputs</button>
<?php $this->endClip('element-header-additional'); ?>

<?php
if ($this->shouldTrackCviAlert()) {
    echo $form->hiddenInput($element, 'cvi_alert_dismissed', false, array('class' => 'cvi_alert_dismissed'));
}
?>
<div class="element-fields full-width" id="<?= $model_name ?>_form">
    <?php echo $form->hiddenInput($element, 'eye_id', false, ['class' => 'sideField']); ?>
    <?php echo $form->hiddenInput($element, 'record_mode', $element::RECORD_MODE_COMPLEX); ?>

    <div id="<?= $model_name ?>_beo_readings_form"
         class="element-both-eyes js-element-eye beo-eye"
         data-side="beo"
    >
        <div class="active-form"style="<?= $element->hasEye('beo') ? '' : 'display: none;' ?>">
            <div class="remove-side"><i class="oe-i remove-circle small"></i></div>
            <div class="flex-layout">
                <div class="cols-10">
                    <table class="cols-full readings">
                        <thead>
                        <tr>
                            <th><?= $this->getReadingAttributeLabel('method_id') ?></th>
                            <th><?= $this->getReadingAttributeLabel('unit_id') ?></th>
                            <th></th>
                            <th><?= $this->getReadingAttributeLabel('source_id') ?></th>
                            <?php if ($this->readingsHaveFixation()) { ?>
                                <th><?= $this->getReadingAttributeLabel('fixation_id') ?></th>
                            <?php } ?>
                            <th><?= $this->getReadingAttributeLabel('occluder_id') ?></th>
                            <th><?= $this->getReadingAttributeLabel('with_head_posture') ?></th>
                            <th><!-- trash icon --></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?= $this->renderReadingsForElement($element->beo_readings) ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="data-group no-readings flex-layout flex-right">
                <div class="cols-5 end">
                    <?= $form->checkBox($element, 'beo_unable_to_assess',
                        ['class' => 'js-cannot-record js-cannot-assess', 'text-align' => 'right', 'nowrapper' => true]) ?>
                    <?= $form->checkBox($element, 'beo_behaviour_assessed',
                        ['class' => 'js-cannot-record', 'text-align' => 'right', 'nowrapper' => true]) ?>
                </div>
                <div class="cols-2"></div>
            </div>
            <div id="<?= $model_name ?>-beo-comments"
                 class="flex-layout flex-left comment-group js-comment-container"
                 style="<?= !$element->beo_notes ? 'display: none;' : '' ?>"
                 data-comment-button="#<?= $model_name ?>-beo-comment-button">
                <?= \CHtml::activeTextArea($element, 'beo_notes',
                    [
                        'rows' => 1,
                        'placeholder' => $element->getAttributeLabel('beo_notes'),
                        'class' => 'cols-full js-comment-field',
                        'style' => 'overflow-wrap: break-word; height: 24px;',
                    ]) ?>
                <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
            </div>
            <div class="flex-layout flex-right">
                <!-- use flex to position the 'add-data-actions' -->
                <div class="add-data-actions flex-item-bottom ">
                    <button
                        id="<?= $model_name ?>-beo-comment-button"
                        class="button js-add-comments"
                        data-comment-container="#<?= $model_name ?>-beo-comments"
                        type="button"
                        style="<?= $element->beo_notes ? "display: none;" : "" ?>"
                    >
                        <i class="oe-i comments small-icon "></i>
                    </button>
                    <button class="button hint green  js-add-select-btn" data-adder-trigger="true"><i
                            class="oe-i plus pro-theme"></i></button><!-- popup to add data to element -->
                </div>
            </div>
        </div>
        <div class="inactive-form" style="<?= $element->hasEye('beo') ? 'display: none;' : '' ?> ">
            <div class="add-side">
                <a href="#" data-test="<?= $test_prefix ?>-add-beo">
                    Add BEO <span class="icon-add-side"></span>
                </a>
            </div>
        </div>
        <template class="hidden" data-entry-template="true">
            <?= $this->renderReadingTemplateForSide('beo') ?>
        </template>
        <script type="text/javascript">
            $(document).ready(function () {
                new OpenEyes.OphCiExamination.VisualAcuityController({
                    container: document.querySelector('#<?= $model_name ?>_beo_readings_form'),
                    vaUnitOptions: OpenEyes.OphCiExamination.VisualAcuityUnitOptions["<?= $model_name ?>"]
                });
            });
        </script>
    </div>
    <div class="element-eyes">
        <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) { ?>
        <div id="<?= $model_name ?>_<?= $eye_side ?>_readings_form"
             class="js-element-eye <?= $eye_side ?>-eye <?= $page_side ?>"
             data-side="<?= $eye_side ?>">
            <div class="active-form" style="<?= $element->hasEye($eye_side) ? '' : 'display: none;' ?>">
                <div class="remove-side"><i class="oe-i remove-circle small"></i></div>

                    <table id="<?= $model_name ?>_<?= $eye_side ?>_readings" class="cols-full readings">
                        <thead>
                        <tr>
                            <th><?= $this->getReadingAttributeLabel('method_id') ?></th>
                            <th><?= $this->getReadingAttributeLabel('unit_id') ?></th>
                            <th></th>
                            <th><?= $this->getReadingAttributeLabel('source_id') ?></th>
                            <th><?= $this->getReadingAttributeLabel('fixation_id') ?></th>
                            <th><?= $this->getReadingAttributeLabel('occluder_id') ?></th>
                            <th><?= $this->getReadingAttributeLabel('with_head_posture') ?></th>
                            <th><!-- trash icon --></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?= $this->renderReadingsForElement($element->{"{$eye_side}_readings"}) ?>
                        </tbody>
                    </table>
                    <div class="data-group no-readings">
                        <div class="cols-8 column end">
                            <?php echo $form->checkBox($element, $eye_side . '_unable_to_assess',
                                ['class' => 'js-cannot-record js-cannot-assess', 'text-align' => 'right', 'nowrapper' => true, 'data-test' => 'unable_to_assess-input']) ?>
                            <?php echo $form->checkBox($element, $eye_side . '_eye_missing',
                                ['class' => 'js-cannot-record js-cannot-assess', 'text-align' => 'right', 'nowrapper' => true, 'data-test' => 'eye_missing-input']) ?>
                            <?php echo $form->checkBox($element, $eye_side . '_behaviour_assessed',
                                ['class' => 'js-cannot-record', 'text-align' => 'right', 'nowrapper' => true, 'data-test' => 'behaviour_assessed-input']) ?>
                        </div>
                    </div>
                    <div id="<?= $model_name ?>-<?= $eye_side ?>-comments"
                         class="flex-layout flex-left comment-group js-comment-container"
                         style="<?= !$element->{$eye_side . '_notes'} ? 'display: none;' : '' ?>"
                         data-comment-button="#<?= $model_name ?>-<?= $eye_side ?>-comment-button">
                        <?= \CHtml::activeTextArea($element, $eye_side . '_notes',
                            array(
                                'rows' => 1,
                                'placeholder' => $element->getAttributeLabel($eye_side . '_notes'),
                                'class' => 'cols-full js-comment-field',
                                'style' => 'overflow-wrap: break-word; height: 24px;',
                            )) ?>
                        <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
                    </div>
                    <div class="flex-layout flex-right">
                        <!-- use flex to position the 'add-data-actions' -->
                        <div class="add-data-actions flex-item-bottom ">
                            <button
                                id="<?= $model_name ?>-<?= $eye_side ?>-comment-button"
                                class="button js-add-comments"
                                data-comment-container="#<?= $model_name ?>-<?= $eye_side ?>-comments"
                                type="button"
                                style="<?= $element->{"{$eye_side}_notes"} ? "display: none;" : "" ?>"
                            >
                                <i class="oe-i comments small-icon "></i>
                            </button>
                            <button class="button hint green  js-add-select-btn" data-adder-trigger="true"><i
                                    class="oe-i plus pro-theme"></i></button><!-- popup to add data to element -->
                    </div>
                </div>
            </div>
            <div class="inactive-form" style="<?= $element->hasEye($eye_side) ? 'display: none;' : '' ?> ">
                <div class="add-side">
                    <a href="#">
                        Add <?= $eye_side ?> side <span class="icon-add-side"></span>
                    </a>
                </div>
            </div>
            <template class="hidden" data-entry-template="true">
                <?= $this->renderReadingTemplateForSide($eye_side) ?>
            </template>
        </div>

        <script type="text/javascript">
            $(document).ready(function () {
                new OpenEyes.OphCiExamination.VisualAcuityController({
                    container: document.querySelector('#<?= $model_name ?>_<?= $eye_side ?>_readings_form'),
                    vaUnitOptions: OpenEyes.OphCiExamination.VisualAcuityUnitOptions["<?= $model_name ?>"],
                    trackCvi: <?= $this->shouldTrackCviAlert() ? 'true' : 'false' ?>
                });
            });
        </script>

        <?php } ?>
    </div>
</div>

