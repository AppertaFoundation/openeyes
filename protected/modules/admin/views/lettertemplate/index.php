<?php
$this->breadcrumbs=array(
	'Lettertemplates',
);

$this->menu=array(
	array('label'=>'Create Lettertemplate', 'url'=>array('create')),
	array('label'=>'Manage Lettertemplate', 'url'=>array('admin')),
);
?>

<h1>Lettertemplates</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
