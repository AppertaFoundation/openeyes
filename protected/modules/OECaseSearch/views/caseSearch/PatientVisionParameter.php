<?php
    use OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity;

    $ops = array(
        '<' => 'Worse than',
        '>' => 'Better than',
        'BETWEEN' => 'Between',
    );
    $va_values = Element_OphCiExamination_VisualAcuity::model()->getUnitValuesForForm(
        OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit::model()->findByAttributes(array('name'=>'ETDRS Letters'))->id,
        false
    );
    $va_values = $va_values[0];

    ?>
<div class="flex-layout cols-full">
    Vision range (ETDRS Letters)
    <?php
    echo CHtml::activeDropDownList(
        $model,
        "[$id]minValue",
        $va_values,
        array('class' => 'cols-3 js-vision-min', 'empty' => 'Min (no min)')
    );
    echo CHtml::error($model, "[$id]minValue");
    echo CHtml::activeDropDownList(
        $model,
        "[$id]maxValue",
        $va_values,
        array('class' => 'cols-3 js-vision-max', 'empty' => 'Max (no max)')
    );
    echo CHtml::error($model, "[$id]maxValue"); ?>
</div>
<label class="inline highlight">
    <?php echo CHtml::activeCheckBox(
        $model,
        "[$id]bothEyesIndicator"
    );?>
    Search for both eyes
</label>