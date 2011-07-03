<?php
$this->breadcrumbs=array(
	'Letter Templates',
);

$this->menu=array(
	array('label'=>'Create Letter Template', 'url'=>array('create')),
);
?>

<h1>Letter Templates</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
