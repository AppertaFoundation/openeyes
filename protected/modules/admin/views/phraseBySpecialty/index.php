<?php
$this->breadcrumbs=array(
	'Phrase By Specialties',
);

$this->menu=array(
	array('label'=>'Create PhraseBySpecialty', 'url'=>array('create')),
	array('label'=>'Manage PhraseBySpecialty', 'url'=>array('admin')),
);
?>

<h1>Phrase By Specialties</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
