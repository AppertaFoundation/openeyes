<?php
$this->breadcrumbs=array(
	'Event Types',
);

$this->menu=array(
	array('label'=>'Create EventType', 'url'=>array('create')),
	array('label'=>'Manage EventType', 'url'=>array('admin')),
);
?>

<h1>Event Types</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
