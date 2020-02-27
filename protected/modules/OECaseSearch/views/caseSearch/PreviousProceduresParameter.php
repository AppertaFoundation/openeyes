<?php
$html = Yii::app()->controller->widget('zii.widgets.jui.CJuiAutoComplete', array(
    'name' => $model->name . $model->id,
    'model' => $model,
    'attribute' => "[$id]textValue",
    'source' => Yii::app()->controller->createUrl('AutoComplete/commonProcedures'),
    'options' => array(
        'minLength' => 2,
    ),
    'htmlOptions' => array(
        'class' => 'search cols-full',
        'placeholder' => 'Previous procedure',
    )
), true);
Yii::app()->clientScript->render($html);
echo $html;
echo CHtml::error($model, "[$id]textValue"); ?>
<label class="inline highlight">
    <?php echo CHtml::activeCheckBox($model, "[$id]operation"); ?>
    has NOT had procedure
</label>
