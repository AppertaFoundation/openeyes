<?php
$this->breadcrumbs=array(
	'Letter Templates'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Letter Template', 'url'=>array('index')),
	array('label'=>'Create Letter Template', 'url'=>array('create')),
	array('label'=>'View Letter Template', 'url'=>array('view', 'id'=>$model->id)),
);
?>

<h1>Update Letter Template <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
