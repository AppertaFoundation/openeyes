<?php
$this->breadcrumbs=array(
	'Letterphrases',
);

$this->menu=array(
	array('label'=>'Create Letterphrase', 'url'=>array('create')),
	array('label'=>'Manage Letterphrase', 'url'=>array('admin')),
);
?>

<h1>Letterphrases</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
