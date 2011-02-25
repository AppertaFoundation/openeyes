<?php
$this->breadcrumbs=array(
	'Site Element Types'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List SiteElementType', 'url'=>array('index')),
	array('label'=>'Create SiteElementType', 'url'=>array('create')),
	array('label'=>'Update SiteElementType', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete SiteElementType', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage SiteElementType', 'url'=>array('admin')),
);
?>

<h1>View SiteElementType #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'possible_element_type_id',
		'specialty_id',
		'view_number',
		'default',
		'first_in_episode',
	),
)); ?>
