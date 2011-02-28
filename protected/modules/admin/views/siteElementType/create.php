<?php
$this->breadcrumbs=array(
	'Site Element Types'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List SiteElementType', 'url'=>array('index')),
	array('label'=>'Manage SiteElementType', 'url'=>array('admin')),
);
?>

<h1>Create SiteElementType</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>