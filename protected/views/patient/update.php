<?php
$this->breadcrumbs=array(
	'Patients'=>array('index'),
	$model->title=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Patient', 'url'=>array('index')),
	array('label'=>'Create Patient', 'url'=>array('create')),
	array('label'=>'View Patient', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Patient', 'url'=>array('admin')),
);
?>

<h1>Update Patient <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>