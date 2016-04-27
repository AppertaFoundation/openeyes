<?php
/* @var $this PatientMergeRequestController */
/* @var $model PatientMergeRequest */

?>

<h1 class="badge">View PatientMergeRequest #<?php echo $model->id; ?></h1>

<div id="patientMergeWrapper" class="container content">
    
    <div class="row">
        <div class="large-3 column large-centered text-right large-offset-9">
            <section class="box dashboard">
            <?php 
                echo CHtml::link('list',array('patientmergerequest/index'), array('class' => 'button small'));
                echo CHtml::link('create',array('patientmergerequest/create'), array('class' => 'button small secondary'));
            ?>
            </section>
        </div>
    </div>

<div class="row">
    <div class="large-8 column large-centered">
        <?php $this->widget('zii.widgets.CDetailView', array(
                'data'=>$model,
                'attributes'=>array(
                        'id',
                        'primary_id',
                        'primary_hos_num',
                        'primary_nhsnum',
                        'primary_dob',
                        'primary_gender',
                        'secondary_id',
                        'secondary_hos_num',
                        'secondary_nhsnum',
                        'secondary_dob',
                        'secondary_gender',
                        'merge_json',
                        'comment',
                        'status',
                        'last_modified_user_id',
                        'last_modified_date',
                        'created_user_id',
                        'created_date',
                ),
        )); ?>
        <br>
    </div>
</div>
