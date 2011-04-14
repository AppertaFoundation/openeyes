<?php
$this->breadcrumbs=array(
	'Phrase By Firms'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List PhraseByFirm', 'url'=>array('index')),
	array('label'=>'Manage PhraseByFirm', 'url'=>array('admin')),
);
?>

<h1>Create PhraseByFirm</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>