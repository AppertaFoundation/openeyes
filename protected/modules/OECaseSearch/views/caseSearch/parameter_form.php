<?php
/* @var $this CaseSearchController */
/* @var $id int */
/* @var $model CaseSearchParameter */
/* @var $form CActiveForm */
?>

<div id="<?php echo $id; ?>" class="<?php echo $model->name; ?> parameter flex-layout">
    <div class="cols-10">
        <?php $model->renderParameter($id); ?>
        <?php echo CHtml::activeHiddenField($model, "[$id]id"); ?>
    </div>
    <div class="cols-2">
        <i id="<?= $id?>-remove" class="oe-i trash"></i>
<!--        <p>--><?php //echo CHtml::link('Remove', 'javascript:void(0)', array('onclick' => 'removeParam(this)', 'class' => 'remove-link')); ?><!--</p>-->
    </div>
    <hr/>
</div>
<script type="text/javascript">
    $('#<?= $id?>-remove').on('click', function () {
        this.closest('.parameter').remove();
    })
</script>
