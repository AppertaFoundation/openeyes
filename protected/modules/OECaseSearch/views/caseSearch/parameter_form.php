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
    case '=':
        $operation_display = 'IS';
        break;
    case '!=':
        $operation_display = 'IS NOT';
        break;
    default:
        $operation_display = $model->operation;
        break;
}
?>
<tr class="parameter" id="<?= $id ?>">
    <td>
        <?= $model->getDisplayTitle() ?>
        <?= !$readonly ? CHtml::hiddenField(CHtml::modelName($model) . "[$id][type]", CHtml::modelName($model)) : '' ?>
        <?= !$readonly ? CHtml::activeHiddenField($model, "[$id]id") : '' ?>
    </td>
    <td>
        <?= $operation_display ?>
        <?= !$readonly ? CHtml::activeHiddenField($model, "[$id]operation") : '' ?>
    </td>
    <?php if ($model->value && !is_array($model->value)) { ?>
    <td>
        <?= $model->getValueForAttribute('value') ?>
        <?= !$readonly ? CHtml::activeHiddenField($model, "[$id]value") : '' ?>
    </td>
    <?php }
    foreach ($model->attributeNames() as $attribute) {
        if (strpos('operation, id, name, isFixed, value', $attribute) === false) { ?>
            <td>
                <?= $model->getValueForAttribute($attribute) ?>
                <?= !$readonly ? CHtml::activeHiddenField($model, "[$id]$attribute") : '' ?>
            </td>
        <?php }
    } ?>
    <?php if (!$readonly) : ?>
    <td>
        <i id="<?= $id ?>-remove" class="oe-i remove-circle small"></i>
    </td>
    <?php endif; ?>
</tr>



