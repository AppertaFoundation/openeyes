<?php
$this->breadcrumbs=array(
	'Phrase By Firm'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List PhraseByFirm', 'url'=>array('index')),
);
?>

<h1>Create PhraseByFirm</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
