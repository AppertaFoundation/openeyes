<?php
/* @var $this DisorderController */
/* @var $model Disorder */

$this->breadcrumbs=array(
    'Disorders'=>array('index'),
    'Manage',
);

$this->menu=array(
    array('label'=>'List Disorder', 'url'=>array('index')),
    array('label'=>'Create Disorder', 'url'=>array('create')),
);

?>

<div class="find-by" style="font-size: 0.6875rem; font-weight: 400; color: #8c8c8c;">
    You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
    or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</div>
<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'disorder-grid',
    'dataProvider'=>$model->search(),
    'filter'=>$model,
    'columns'=>array(
        'id',
        'fully_specified_name',
        'term',
        array('name'=>'specialty_id',
              'value'=>array($this, 'getSpecialtyNameFromId'),
              'header'=>'Specialty'),
        array(
            'class'=>'CButtonColumn',
            'deleteButtonOptions'=>array('class' => 'oe-i trash small hint'),
            'deleteButtonImageUrl'=> '',
            'deleteButtonLabel'=>'',
            'updateButtonOptions'=>array('class' => 'oe-i pencil small'),
            'updateButtonImageUrl'=> '',
            'updateButtonLabel'=>'',
            'viewButtonOptions'=>array('class' => 'oe-i info small'),
            'viewButtonImageUrl'=> '',
            'viewButtonLabel'=> '',
        ),
    ),
    'pager'=>array('class'=>'LinkPager'),
    'pagerCssClass'=>'pagination',
    'itemsCssClass'=>'standard highlight-rows',
)); ?>
<?= CHtml::link('Add New Disorder', Yii::app()->createURL('disorder/create'),array('class' => 'button large')) ?>
