<?php
$this->breadcrumbs=array(
	'Sequences',
);

$this->menu=array(
	array('label'=>'Create Sequence', 'url'=>array('create')),
	array('label'=>'Manage Sequence', 'url'=>array('admin')),
);
?>

<h1>Sequences</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
