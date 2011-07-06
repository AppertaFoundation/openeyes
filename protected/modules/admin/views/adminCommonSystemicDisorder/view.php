<?php
$this->breadcrumbs=array(
	'Common Systemic Disorders'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List CommonSystemicDisorder', 'url'=>array('index')),
	array('label'=>'Create CommonSystemicDisorder', 'url'=>array('create')),
	array('label'=>'Update CommonSystemicDisorder', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete CommonSystemicDisorder', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage CommonSystemicDisorder', 'url'=>array('admin')),
);
?>

<h1>View CommonSystemicDisorder #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		array(
			'name' => 'disorder',
			'value' => CHtml::encode($model->disorder->term)
		)
	),
)); ?>
