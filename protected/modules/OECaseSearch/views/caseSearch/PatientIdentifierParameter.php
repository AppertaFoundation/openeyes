<?php
echo CHtml::activeDropDownList(
    $model,
    "[$id]code",
    $model->getAllCodes(),
    array('onchange' => 'refreshValues(this)','prompt' => 'Select One...', 'class' => 'js-code')
);
echo CHtml::error($model, "[$id]code");
echo CHtml::activeTextField($model, "[$id]number");
echo CHtml::error($model, "[$id]number");
