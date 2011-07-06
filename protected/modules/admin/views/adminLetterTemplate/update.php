<?php
$this->breadcrumbs=array(
	'Lettertemplates'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Lettertemplate', 'url'=>array('index')),
	array('label'=>'Create Lettertemplate', 'url'=>array('create')),
	array('label'=>'View Lettertemplate', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Lettertemplate', 'url'=>array('admin')),
);
?>

<h1>Update Lettertemplate <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>