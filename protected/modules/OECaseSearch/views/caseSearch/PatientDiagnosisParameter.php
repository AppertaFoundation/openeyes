<?php
$firms = Firm::model()->getListWithSpecialties();
$html = Yii::app()->controller->widget('zii.widgets.jui.CJuiAutoComplete', array(
    'name' => $model->name . $model->id,
    'model' => $model,
    'attribute' => "[$id]term",
    'source' => $this->createUrl('AutoComplete/commonDiagnoses'),
    'options' => array(
        'minLength' => 2,
    ),
    'htmlOptions' => array(
        'placeholder' => 'Diagnoses',
        'class' => 'search cols-full'
    ),
), true);
Yii::app()->clientScript->render($html);
echo $html;
echo CHtml::error($model, "[$id]term"); ?>
<div class="flex-layout">
    <label class="inline highlight">
        <?php echo CHtml::activeCheckbox($model, "[$id]operation"); ?>
        has NOT
    </label>
    by
    <?php echo CHtml::activeDropDownList(
        $model,
        "[$id]firm_id",
        $firms,
        array('empty' => 'Any ' . Firm::contextLabel(), 'class' => 'cols-6')
    ); ?>
</div>
<label class="inline highlight">
    <?php echo CHtml::activeCheckBox($model, "[$id]only_latest_event"); ?>
    only include patient's latest event
</label>