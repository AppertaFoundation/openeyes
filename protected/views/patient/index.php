<?php
$this->breadcrumbs=array(
	'Patients',
);

$this->menu=array(
	array('label'=>'Create Patient', 'url'=>array('create')),
	array('label'=>'Manage Patient', 'url'=>array('admin')),
);
?>

<h1>Patients</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
