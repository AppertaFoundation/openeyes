<?php
$ops = array(
    '=' => 'has',
    '!=' => 'does not have',
);

$relatives = CHtml::listData(OEModule\OphCiExamination\models\FamilyHistoryRelative::model()->findAll(), 'id', 'name');
$sides = CHtml::listData(OEModule\OphCiExamination\models\FamilyHistorySide::model()->findAll(), 'id', 'name');
$conditions = CHtml::listData(OEModule\OphCiExamination\models\FamilyHistoryCondition::model()->findAll(), 'id', 'name');
?>
<div class="flex-layout">
    Family history
    <?php echo CHtml::activeDropDownList($model, "[$id]side", $sides, array('empty' => 'Any side', 'class' => 'cols-4')); ?>
    <?php echo CHtml::activeDropDownList(
        $model,
        "[$id]relative",
        $relatives,
        array('empty' => 'Any relative', 'class' => 'cols-4')
    ); ?>
</div>
<div class="flex-layout">
    <label class="inline highlight"><?php echo CHtml::activeCheckbox($model, "[$id]operation"); ?>does NOT have</label>
    <?php echo CHtml::activeDropDownList(
        $model,
        "[$id]condition",
        $conditions,
        array('prompt' => 'Select condition', 'class' => 'cols-7')
    ); ?>
</div>
