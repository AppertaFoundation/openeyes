<div class="eyedraw-fields">
    <?=\CHtml::activeHiddenField($element, $side . '_ed_report'); ?>
    <div class="collapse in">
    <div class="cols-12 column end autoreport-display">
        <span id="<?= CHtml::modelName($element) . '_' . $side . '_ed_report_display' ?>" class="data-value"> </span>
    </div>
    </div>
    <?= CHtml::activeTextArea($element, $side . '_comments', array(
        'rows' => '1',
        'cols' => '20',
        'class' => 'clearWithEyedraw',
        'placeholder' => $element->getAttributeLabel($side . '_comments'),
    )) ?>
    </div>
