<?php
/* @var $this PatientMergeRequestController */
/* @var $dataProvider CActiveDataProvider */

?>

<h1 class="badge">Patient Merge Request List</h1>


<div id="patientMergeWrapper" class="container content">
    
    <?php $this->renderPartial('//base/_messages')?>

    <div class="row">
        <div class="large-3 column large-centered text-right large-offset-9">
            <section class="box dashboard">
            <?php 
                echo CHtml::link('create',array('patientmergerequest/create'), array('class' => 'button small secondary'));
            ?>
            </section>
        </div>
    </div>
    <div class="row">
        <div class="large-8 column large-centered">
            
            <section class="box requestList js-toggle-container">
                <div class="grid-view" id="inbox-table">
                    <?php

    $this->widget('zii.widgets.grid.CGridView', array(
        'itemsCssClass' => 'grid',
        'dataProvider' => $dataProvider,
        'summaryText' => '<h3><small> {start}-{end} of {count} </small></h3>',
        'htmlOptions' => array('id' => 'patientMergeList'),
        'columns' => array(
            array(
                'name' => 'Secondary',
                'header' => $dataProvider->getSort()->link('secondary_hos_num','Secondary',array('class'=>'sort-link')),
                // not ideal using the error class, but a simple solution for now.
                'value' => function($data) {
                    return $data->secondary_hos_num;
                },
                //'type' => 'raw',
                'htmlOptions' => array(
                    'class' => 'secondary'
                )
            ),
            
            array(
                'name' => '',
                // not ideal using the error class, but a simple solution for now.
                'value' => function($data) {
                    return 'INTO';
                },
                //'type' => 'raw',
                'htmlOptions' => array(
                    'class' => 'into'
                )
            ),
            
            array(
                'name' => 'Primary',
                'header' => $dataProvider->getSort()->link('primary_hos_num','Primary',array('class'=>'sort-link')),
                'value' => function($data) {
                    return $data->primary_hos_num;
                },
                //'type' => 'raw',
                'htmlOptions' => array(
                    'class' => 'primary'
                )
            ),
                        
            array(
                'name' => 'Status',
                'header' => $dataProvider->getSort()->link('status','Status',array('class'=>'sort-link')),
                'value' => function($data) {
                    $html = '<div class="circle ' . (strtolower(str_replace(" ", "-", $data->getStatusText())) ) . '"></div> ';
                    $html .= $data->getStatusText();
                    
                    return $html;
                },
                'type' => 'raw',
                'htmlOptions' => array(
                    'class' => 'status'
                )
            ),
                        
            array(
                'name' => '',
                
                'value' => function($data) {
                    $html = "";
                
                    if($data->status == $data::STATUS_NOT_PROCESSED){
                        $html = CHtml::link('merge',array('patientmergerequest/merge', 'id' => $data->id), array('class' => 'warning button small right'));
                    }
                    
                    if($data->status == $data::STATUS_CONFLICT){
                        $html = CHtml::link('edit conflict',array('patientmergerequest/editConflict', 'id' => $data->id), array('class' => 'warning button small right'));
                    }
                    
                    if($data->status == $data::STATUS_MERGED){
                        $html = '<span class="mergedOn">Merged on ' . $data->last_modified_date . '</span>';
                    }
                    

                    return $html;
                    
                },
                'type' => 'raw',
                'htmlOptions' => array(
                    'class' => 'actions text-right'
                )
            ),
                        
            
            
        )
    ));

?>
                   
            </section>
        </div>
    </div>
</div>


