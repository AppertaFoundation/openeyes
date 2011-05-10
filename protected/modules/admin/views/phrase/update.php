<?php
$this->breadcrumbs=array(
	'Global phrases'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Phrase', 'url'=>array('index')),
	array('label'=>'Create Phrase', 'url'=>array('create')),
	array('label'=>'View Phrase', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Phrase', 'url'=>array('admin')),
);
?>

<h1>Update global phrase: <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
