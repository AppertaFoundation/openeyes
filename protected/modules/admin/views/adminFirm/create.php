<?php
$this->breadcrumbs=array(
	'Firms'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Firm', 'url'=>array('index')),
	array('label'=>'Manage Firm', 'url'=>array('admin')),
);
?>

<h1>Create Firm</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>