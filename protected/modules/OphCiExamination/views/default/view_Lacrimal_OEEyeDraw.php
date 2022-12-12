<?php $this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
    'idSuffix' => $side . '_' . $element->elementType->id . '_' . $element->id,
    'side' => ($side == 'right') ? 'R' : 'L',
    'mode' => 'view',
    'width' => $this->action->id === 'view' ? 200 : 120,
    'height' => $this->action->id === 'view' ? 200 : 120,
    'model' => $element,
    'attribute' => $side . '_eyedraw',
)) ?>

<div class="eyedraw-data stack">
    <div class="data-value"><?= Yii::app()->format->Ntext($element->{$side . '_ed_report'}) ?></div>
    <?php if ($element->{$side . '_comments'}) : ?>
        <div class="data-value"><?= Yii::app()->format->Ntext($element->{$side . '_comments'}) ?></div>
    <?php endif; ?>
</div>
