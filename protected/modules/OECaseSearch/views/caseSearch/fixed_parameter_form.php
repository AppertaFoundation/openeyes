<?php
/* @var $id int */
/* @var $this CaseSearchController */
/* @var $model CaseSearchParameter */
/* @var $form CActiveForm */
?>

<div id="<?php echo $id; ?>" class="row field-row">
    <?php $this->renderPartial($model->getViewPath(), array(
        'model' => $model,
        'id' => $id
    )); ?>
</div>