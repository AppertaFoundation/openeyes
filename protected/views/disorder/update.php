<?php
/* @var $this DisorderController */
/* @var $model Disorder */

$this->breadcrumbs=array(
    'Disorders'=>array('index'),
    $model->id=>array('view','id'=>$model->id),
    'Update',
);

$this->menu=array(
    array('label'=>'List Disorder', 'url'=>array('index')),
    array('label'=>'Create Disorder', 'url'=>array('create')),
    array('label'=>'View Disorder', 'url'=>array('view', 'id'=>$model->id)),
    array('label'=>'Manage Disorder', 'url'=>array('admin')),
);
?>

<h1>Update Disorder <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>