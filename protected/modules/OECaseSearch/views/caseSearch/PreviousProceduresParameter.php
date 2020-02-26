<?php
    $ops = array(
        'HAS_HAD' => 'Has had a',
        'HAS_NOT_HAD' => 'Has not had a',
    );
    ?>

<div class="flex-layout flex-left js-case-search-param">
    <div class="parameter-option">
        <?= $model->getDisplayTitle() ?>
    </div>
        <div style="padding-right: 15px;">
            <?php echo CHtml::activeDropDownList($model, "[$id]operation", $ops, array('prompt' => 'Select One...')); ?>
            <?php echo CHtml::error($model, "[$id]operation"); ?>
        </div>

    <div>
        <?php
        $html = Yii::app()->controller->widget('zii.widgets.jui.CJuiAutoComplete', array(
            'name' => $model->name . $model->id,
            'model' => $model,
            'attribute' => "[$id]textValue",
            'source' => Yii::app()->controller->createUrl('AutoComplete/commonProcedures'),
            'options' => array(
                'minLength' => 2,
            ),
        ), true);
        Yii::app()->clientScript->render($html);
        echo $html;
        ?>
        <?php echo CHtml::error($model, "[$id]textValue"); ?>
    </div>
</div>