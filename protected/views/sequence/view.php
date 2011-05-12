<?php
$this->breadcrumbs=array(
	'Sequences'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Sequence', 'url'=>array('index')),
	array('label'=>'Create Sequence', 'url'=>array('create')),
	array('label'=>'Update Sequence', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Sequence', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Sequence', 'url'=>array('admin')),
);
?>

<h1>View Sequence #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		array(
			'label' => 'Firm',
			'value' => $model->getFirmName()
		),
		array(
			'label' => 'Theatre',
			'value' => $model->theatre->site->name . ' - ' . $model->theatre->name
		),
		'start_date',
		'start_time',
		'end_time',
		'end_date',
		'frequency',
	),
)); ?>
