<?php
$this->breadcrumbs=array(
	'Letterphrases'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List Letterphrase', 'url'=>array('index')),
	array('label'=>'Create Letterphrase', 'url'=>array('create')),
	array('label'=>'Update Letterphrase', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Letterphrase', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Letterphrase', 'url'=>array('admin')),
);
?>

<h1>View Letterphrase #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		array(
			'name' => 'firm_id',
			'value' => CHtml::encode($model->firm->name)
		),
		'name',
		'phrase',
		array(
			'name' => 'section',
			'value' => CHtml::encode($model->getSectionText())
		),
		'display_order',
	),
)); ?>
