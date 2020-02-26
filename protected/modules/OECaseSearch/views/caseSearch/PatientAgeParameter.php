<?php
?>
<div class="flex-layout">
    Age range (yrs)
    <?php
    echo CHtml::activeTextField($model, "[$id]minValue", array('placeholder' => 'Min (no min)', 'class' => 'js-age-min cols-4'));
    echo CHtml::error($model, "[$id]minValue");
    echo CHtml::activeTextField($model, "[$id]maxValue", array('placeholder' => 'Max (no max)', 'class' => 'js-age-max cols-4'));
    echo CHtml::error($model, "[$id]maxValue");
    echo CHtml::activeHiddenField($model, "[$id]id"); ?>
</div>
