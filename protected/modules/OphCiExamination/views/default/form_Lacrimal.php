<div class="element-fields element-eyes">
    <?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>
    <?php foreach (array('left' => 'right', 'right' => 'left') as $page_side => $eye_side) : ?>
    <div
        class="js-element-eye <?= $eye_side ?>-eye column <?= $page_side ?> <?= !$element->hasEye($eye_side) ? 'inactive' : '' ?>"
        data-side="<?= $eye_side ?>">
        <div class="active-form" style="<?= !$element->hasEye($eye_side) ? 'display: none;' : '' ?>">
        <a class="remove-side"><i class="oe-i remove-circle small"></i></a>
        <div class="eyedraw-row flex--layout flex-top">
            <?php $this->renderPartial($element->form_view . '_OEEyeDraw', array(
                'form' => $form,
                'side' => $eye_side,
                'element' => $element,
            )) ?>
        </div>
        </div>
        <div class="inactive-form" style="<?= $element->hasEye($eye_side) ? 'display: none;' : '' ?>">
        <div class="add-side">
            <a href="#">
            Add <?= $eye_side ?> side <span class="icon-add-side"></span>
            </a>
        </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php Yii::app()->clientScript->registerScriptFile("{$this->assetPath}/js/AutoReport.js", CClientScript::POS_HEAD); ?>
