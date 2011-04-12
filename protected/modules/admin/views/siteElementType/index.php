<?php
$this->breadcrumbs=array(
	'Site Element Types',
);

$this->menu=array(
	array('label'=>'Create SiteElementType', 'url'=>array('create')),
	array('label'=>'Manage SiteElementType', 'url'=>array('admin')),
);
?>

<h1>Site Element Types</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
