<?php
/* @var $this CaseSearchController */
/* @var $id int */
/* @var $model CaseSearchParameter */
/* @var $form CActiveForm */
?>

<div id="<?php echo $id; ?>" class="<?php echo $model->name; ?> parameter row field-row">
    <div class="large-10 column">
        <?php $model->renderParameter($id); ?>
        <?php echo CHtml::activeHiddenField($model, "[$id]id"); ?>
    </div>
    <div class="large-2 column end">
        <p><?php echo CHtml::link('Remove', 'javascript:void(0)', array('onclick' => 'removeParam(this)', 'class' => 'remove-link')); ?></p>
    </div>
    <hr/>
</div>
