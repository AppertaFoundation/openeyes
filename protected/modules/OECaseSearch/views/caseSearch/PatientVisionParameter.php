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
<div class="flex-layout flex-left js-case-search-param">
    <div class="parameter-option">
        <p><?= $model->getLabel() ?></p>
    </div>
    <div class="parameter-option">
        <?php echo CHtml::activeDropDownList(
            $model,
            "[$id]operation",
            $ops,
            array('onchange' => 'refreshValues(this)', 'prompt' => 'Select One...', 'class' => 'js-vision-operation')
        ); ?>
        <?php echo CHtml::error($model, "[$id]operation"); ?>
    </div>
    <div class="dual-value parameter-option"
         style="<?php echo $model->operation === 'BETWEEN' ? 'display: inline-block;' : 'display: none;' ?>"
    >
        <?php echo CHtml::activeDropDownList(
            $model,
            "[$id]minValue",
            $va_values,
            array( 'class' => 'js-vision-min')
        ); ?>
        <?php echo CHtml::error($model, "[$id]minValue"); ?>
        <?php echo CHtml::activeDropDownList(
            $model,
            "[$id]maxValue",
            $va_values,
            array( 'class' => 'js-vision-max')
        ); ?>
        <?php echo CHtml::error($model, "[$id]maxValue"); ?>
    </div>
    <div class="single-value parameter-option"
         style="<?php echo $model->operation !== 'BETWEEN' ? 'display: inline-block;' : 'display: none;' ?>"
    >
        <?php echo CHtml::activeDropDownList(
            $model,
            "[$id]textValue",
            $va_values,
            array('class' => 'js-vision-value')
        ); ?>
        <?php echo CHtml::error($model, "[$id]textValue"); ?>
    </div>
    <div class="parameter-option">
        <p>Search for both eyes</p>
    </div>
    <div class="parameter-option">
        <?php echo CHtml::activeCheckBox(
            $model,
            "[$id]bothEyesIndicator",
            array()
        );?>
    </div>
    <div class="parameter-option">
        <p>ETDRS Letters</p>
    </div>
</div>