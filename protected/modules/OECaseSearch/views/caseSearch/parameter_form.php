<?php
/* @var $this CaseSearchController */
/* @var $id int */
/* @var $model CaseSearchParameter */
/* @var $form CActiveForm */

$operation_display = null;

if (!isset($readonly)) {
    $readonly = false;
}

switch ($model->operation) {
    case '<':
        $operation_display = 'IS LESS THAN';
        break;
    case '>':
        $operation_display = 'IS MORE THAN';
        break;
    case '1':
    case '=':
        $operation_display = 'IS';
        break;
    case '0':
    case '!=':
        $operation_display = 'IS NOT';
        break;
    case 'IN':
        $operation_display = 'INCLUDES';
        break;
    case 'NOT IN':
        $operation_display = 'DOES NOT INCLUDE';
        break;
    default:
        $operation_display = $model->operation;
        break;
}
?>
<tr class="parameter" id="<?= $id ?>">
    <td>
        <?= $model->label ?>
        <?= !$readonly ? CHtml::hiddenField(CHtml::modelName($model) . "[$id][type]", CHtml::modelName($model)) : '' ?>
        <?= !$readonly ? CHtml::activeHiddenField($model, "[$id]id") : '' ?>
    </td>
    <td>
        <?= $operation_display ?>
        <?= !$readonly ? CHtml::activeHiddenField($model, "[$id]operation") : '' ?>
    </td>
    <td>
        <?php
        if ($model->value !== null && !is_array($model->value)) {
            echo $model->getValueForAttribute('value');
            echo !$readonly ? CHtml::activeHiddenField($model, "[$id]value") : '';
        }
        foreach ($model->attributeNames() as $attribute) {
            if (strpos('operation, id, name, value', $attribute) === false) { ?>
                <br/>
                <?= $model->getValueForAttribute($attribute) ?>
                <?= !$readonly ? CHtml::activeHiddenField($model, "[$id]$attribute") : '' ?>
            <?php }
        }?>
    </td>
    <?php if (!$readonly) : ?>
    <td>
        <i id="<?= $id ?>-remove" class="oe-i remove-circle small"></i>
    </td>
    <?php endif; ?>
</tr>



