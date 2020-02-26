<?php
    $ops = array(
        'LIKE' => 'Has taken ',
        'NOT LIKE' => 'Has not taken',
    );

    $html = $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
        'name' => $model->name . $model->id,
        'model' => $model,
        'attribute' => "[$id]textValue",
        'source' => $this->createUrl('AutoComplete/commonMedicines'),
        'options' => array(
            'minLength' => 2,
        ),
        'htmlOptions' => array(
                'class' => 'search cols-full',
            'placeholder' => 'Medication'
        )
    ), true);
    Yii::app()->clientScript->render($html);
    echo $html;
    echo CHtml::error($model, "[$id]textValue"); ?>
<label class="inline highlight">
    <?= CHtml::activeCheckbox($model, "[$id]operation") ?>
    has NOT taken
</label>

