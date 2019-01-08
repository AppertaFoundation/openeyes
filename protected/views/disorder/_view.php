<?php
/* @var $this DisorderController */
/* @var $data Disorder */
?>

<table class="standard highlight-rows">
    <tbody>
        <tr>
            <td>ID: </td>
            <td>
                <?php echo CHtml::link(CHtml::encode($model->id), array('view', 'id'=>$model->id)); ?>
            </td>
        </tr>
    <tr>
        <td>
            Fully Specified Name:
        </td>
        <td>
            <?php echo CHtml::encode($model->fully_specified_name); ?>
        </td>
    </tr>
    <tr>
        <td>
            Term:
        </td>
        <td>
            <?php echo CHtml::encode($model->term); ?>
        </td>
    </tr>
    </tbody>
</table>