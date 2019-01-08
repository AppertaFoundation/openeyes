<?php
/* @var $this DisorderController */
/* @var $model Disorder */

$this->breadcrumbs=array(
	'Disorders'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Disorder', 'url'=>array('index')),
	array('label'=>'Create Disorder', 'url'=>array('create')),
	array('label'=>'Update Disorder', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Disorder', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Disorder', 'url'=>array('admin')),
);
?>

<h1>View Disorder #<?php echo $model->id; ?></h1>

<table class="standard highlight-rows">
    <tbody>
    <tr>
        <td>ID: </td>
        <td>
            <?= CHtml::link(CHtml::encode($model->id), array('view', 'id'=>$model->id)); ?>
        </td>
    </tr>
    <tr>
        <td>
            Fully Specified Name:
        </td>
        <td>
            <?= CHtml::encode($model->fully_specified_name); ?>
        </td>
    </tr>
    <tr>
        <td>
            Term:
        </td>
        <td>
            <?= CHtml::encode($model->term); ?>
        </td>
    </tr>
    <tr>
        <td>
            Specialty:
        </td>
        <td>
            <?= isset($model->specialty_id)? Specialty::model()->findByPk($model->specialty_id)->name: ''; ?>
        </td>
    </tr>
    <tr>
        <td>
            Active:
        </td>
        <td>
            <?= $model->active?'True': 'False'; ?>
        </td>
    </tr>
    </tbody>
</table>