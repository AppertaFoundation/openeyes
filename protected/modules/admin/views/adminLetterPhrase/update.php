<?php
$this->breadcrumbs=array(
	'Letterphrases'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Letterphrase', 'url'=>array('index')),
	array('label'=>'Create Letterphrase', 'url'=>array('create')),
	array('label'=>'View Letterphrase', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Letterphrase', 'url'=>array('admin')),
);
?>

<h1>Update Letterphrase <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>