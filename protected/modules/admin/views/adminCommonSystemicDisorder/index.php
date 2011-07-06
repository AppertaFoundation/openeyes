<?php
$this->breadcrumbs=array(
	'Common Systemic Disorders',
);

$this->menu=array(
	array('label'=>'Create CommonSystemicDisorder', 'url'=>array('create')),
	array('label'=>'Manage CommonSystemicDisorder', 'url'=>array('admin')),
);
?>

<h1>Common Systemic Disorders</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
