<?php
$this->breadcrumbs=array(
	'Examphrases'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Examphrase', 'url'=>array('index')),
	array('label'=>'Create Examphrase', 'url'=>array('create')),
	array('label'=>'View Examphrase', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Examphrase', 'url'=>array('admin')),
);
?>

<h1>Update Examphrase <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>