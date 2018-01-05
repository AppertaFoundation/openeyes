<?php
/* @var $this OphCiExaminationRiskController */
/* @var $model OphCiExaminationRisk */
?>


<div class="box admin">
    <h2>Common Ophthalmic Disorder</h2>

<?php

    $columns = array(
        'name',
        array(
            'header' => 'Subspecialty',
            'name' => 'subspecialty_id',
            'type' => 'raw',
            'value' => function($data, $row){

                $options = CHtml::listData(\Subspecialty::model()->findAll(), 'id', 'name');
                return CHtml::activeDropDownList($data, "[$row]subspecialty_id", $options, array('empty' => '-- select --'));
            }
        ),
        array(
            'header' => 'Firm',
            'name' => 'firm_id',
            'type' => 'raw',
            'value' => function($data, $row){
                $options = CHtml::listData(\Firm::model()->findAll(), 'id', 'name');
                return CHtml::activeDropDownList($data, "[$row]firm_id", $options, array('empty' => '-- select --'));
            }
        ),
        array(
            'header' => 'Firm',
            'name' => 'firm_id',
            'type' => 'raw',
            'value' => function($data, $row){
                $options = CHtml::listData(\Gender::model()->findAll(), 'id', 'name');
                return CHtml::activeDropDownList($data, "[$row]gender", $options, array('empty' => '-- select --'));
            }
        ),



    );

    $dataProvider = $model->search();
    $dataProvider->pagination = false;


    $this->widget('zii.widgets.grid.CGridView', array(
        'dataProvider' => $dataProvider,
        'itemsCssClass' => 'generic-admin',
        //'template' => '{items}',
        "emptyTagName" => 'span',
        'summaryText' => false,
        'rowHtmlOptionsExpression'=>'array("data-row"=>$row)',
        'enableSorting' => false,
        'enablePagination' => false,
        'columns' => $columns,

    ));

?>


<?php
/*
$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'oph-ci-examination-risk-grid',
    'dataProvider'=>$model->search(),
    'filter'=>$model,
    'columns'=>array(
        'id',
        'active',
        'name',
        'subspecialty_id',
        'firm_id',
        'episode_status_id',

        'gender',
        'age_min',
        'age_max',

        array(
            'class'=>'CButtonColumn',
        ),
    ),
)); */ ?>

</div>
