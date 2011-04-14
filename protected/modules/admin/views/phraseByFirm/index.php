<?php
$this->breadcrumbs=array(
	'Phrase By Firms',
);

$this->menu=array(
	array('label'=>'Create PhraseByFirm', 'url'=>array('create')),
	array('label'=>'Manage PhraseByFirm', 'url'=>array('admin')),
);
?>

<h1>Phrase By Firms</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
