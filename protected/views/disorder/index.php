<?php
/* @var $this DisorderController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Disorders',
);

$this->menu=array(
	array('label'=>'Create Disorder', 'url'=>array('create')),
	array('label'=>'Manage Disorder', 'url'=>array('admin')),
);
?>

<h1>Disorders</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
