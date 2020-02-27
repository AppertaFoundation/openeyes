<?php
$ops = array(
    '=' => 'Is allergic to',
    '!=' => 'Is not allergic to',
);

$html = $this->widget(
    'zii.widgets.jui.CJuiAutoComplete',
    array(
        'name' => $model->name . $model->id,
        'model' => $model,
        'attribute' => "[$id]textValue",
        'source' => $this->createUrl('AutoComplete/commonAllergies'),
        'options' => array(
            'minLength' => 2,
        ),
        'htmlOptions' => array(
            'class' => 'search cols-full',
            'placeholder' => 'Allergies',
        )
    ),
    true
);
Yii::app()->clientScript->render($html);
echo $html;
echo CHtml::error($model, "[$id]textValue");
?>
<label class="inline highlight">
    <?php echo CHtml::activeCheckBox($model, "[$id]operation"); ?>
    <?php echo CHtml::error($model, "[$id]operation"); ?>
    is NOT allergic to
</label>