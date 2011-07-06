<?php
$this->breadcrumbs=array(
	'Firms'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Firm', 'url'=>array('index')),
	array('label'=>'Create Firm', 'url'=>array('create')),
	array('label'=>'View Firm', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Firm', 'url'=>array('admin')),
);
?>

<h1>Update Firm <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>