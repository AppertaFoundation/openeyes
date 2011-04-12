<?php
$this->breadcrumbs=array(
	'Site Element Types'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List SiteElementType', 'url'=>array('index')),
	array('label'=>'View SiteElementType', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage SiteElementType', 'url'=>array('admin')),
);
?>

<h1>Update Site Element Type for:</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
