<?php
$this->breadcrumbs=array(
	'Firms',
);

$this->menu=array(
	array('label'=>'Create Firm', 'url'=>array('create')),
	array('label'=>'Manage Firm', 'url'=>array('admin')),
);
?>

<h1>Firms</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
