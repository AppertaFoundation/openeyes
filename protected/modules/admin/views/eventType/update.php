<?php
$this->breadcrumbs=array(
	'Event Types'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List EventType', 'url'=>array('index')),
	array('label'=>'Create EventType', 'url'=>array('create')),
	array('label'=>'View EventType', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage EventType', 'url'=>array('admin')),
);
?>

<h1>Update EventType <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>