<?php
/* @var $this DisorderController */
/* @var $model Disorder */

$this->breadcrumbs=array(
    'Disorders'=>array('index'),
    'Create',
);

$this->menu=array(
    array('label'=>'List Disorder', 'url'=>array('index')),
    array('label'=>'Manage Disorder', 'url'=>array('admin')),
);
?>

<h1>Create Disorder</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>