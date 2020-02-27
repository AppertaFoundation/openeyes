<?php
/* @var $id int */
/* @var $this CaseSearchController */
/* @var $model CaseSearchParameter */
/* @var $form CActiveForm */
?>

<tr id="<?php echo $id; ?>" class="fixed-parameter">
    <td>
        <?php $this->renderPartial($model->getViewPath(), array(
            'model' => $model,
            'id' => $id
        )); ?>
    </td>
</tr>