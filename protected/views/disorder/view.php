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

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'fully_specified_name',
		'term',
		'last_modified_user_id',
		'last_modified_date',
		'created_user_id',
		'created_date',
		'specialty_id',
		'active',
	),
)); ?>
