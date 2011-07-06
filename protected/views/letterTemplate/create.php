<?php
$this->breadcrumbs=array(
	'LetterTemplates'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Letter Templates', 'url'=>array('index')),
);
?>

<h1>Create Letter Template</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
