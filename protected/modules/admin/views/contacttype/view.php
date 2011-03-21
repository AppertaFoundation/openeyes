<?php
$this->breadcrumbs=array(
	'Contact Types'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List ContactType', 'url'=>array('index')),
	array('label'=>'Create ContactType', 'url'=>array('create')),
	array('label'=>'Update ContactType', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete ContactType', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage ContactType', 'url'=>array('admin')),
);
?>

<h1>View ContactType #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name'
	),
)); ?>
