<?php
$this->breadcrumbs=array(
	'Global phrases'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Phrase', 'url'=>array('index')),
);
?>

<h1>Create PhraseByFirm</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
