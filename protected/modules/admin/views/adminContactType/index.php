<?php
$this->breadcrumbs=array(
	'Contact Types',
);

$this->menu=array(
	array('label'=>'Create ContactType', 'url'=>array('create')),
	array('label'=>'Manage ContactType', 'url'=>array('admin')),
);
?>

<h1>Contact Types</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
