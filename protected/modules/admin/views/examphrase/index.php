<?php
$this->breadcrumbs=array(
	'Examphrases',
);

$this->menu=array(
	array('label'=>'Create Examphrase', 'url'=>array('create')),
	array('label'=>'Manage Examphrase', 'url'=>array('admin')),
);
?>

<h1>Examphrases</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
