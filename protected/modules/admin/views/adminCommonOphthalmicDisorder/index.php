<?php
$this->breadcrumbs=array(
	'Common Ophthalmic Disorders',
);

$this->menu=array(
	array('label'=>'Create CommonOphthalmicDisorder', 'url'=>array('create')),
	array('label'=>'Manage CommonOphthalmicDisorder', 'url'=>array('admin')),
);
?>

<h1>Common Ophthalmic Disorders</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
