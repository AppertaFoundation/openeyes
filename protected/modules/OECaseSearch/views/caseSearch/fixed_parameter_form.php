<?php
/* @var $id int */
/* @var $this CaseSearchController */
/* @var $model CaseSearchParameter */
/* @var $form CActiveForm */
?>

<tr id="<?= $id ?>" class="fixed-parameter">
    <td colspan="3">
        <?php $this->renderPartial($model->getViewPath(), array(
            'model' => $model,
            'id' => $id
        )); ?>
    </td>
</tr>