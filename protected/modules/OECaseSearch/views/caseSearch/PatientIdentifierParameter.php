<div class="flex-layout">
<?php
    echo CHtml::activeDropDownList(
        $model,
        "[$id]code",
        $model->getAllCodes(),
        array('prompt' => 'Select One...', 'class' => 'cols-4 js-code')
    );
    echo CHtml::error($model, "[$id]code");
    echo CHtml::activeTextField($model, "[$id]number", array('class' => 'cols-6 search'));
    echo CHtml::error($model, "[$id]number"); ?>
</div>

