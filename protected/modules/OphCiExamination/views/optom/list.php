<?php

$cols = array(
    array(
        'id'        => 'created_date',
        'name'      => 'created_date',
        'type'      => 'raw',
        'header'    => 'Date Received',
        'value'     => 'Helper::convertMySQL2NHS($data->created_date)',
    ),
    array(
        'class'             => 'CLinkColumn',
        'header'            => 'Patient',
        'urlExpression'     => 'Yii::app()->createURL("/patient/view/", array("id" => $data->event->episode->patient_id))',
        'labelExpression'   => '$data->event->episode->patient->getDisplayName() . "<br> (" . $data->event->episode->patient->hos_num . ")"',
    ),
    array(
        'id'        => 'optometrist_name',
        'name'      => 'optometrist_name',
        'type'      => 'raw',
        'header'    => 'Optometrist',
        'value'     => '$data->event->episode->user->first_name ." ". $data->event->episode->user->last_name',
    ),
    array(
        'id'        => 'optometrist_address',
        'name'      => 'optometrist_address',
        'type'      => 'raw',
        'header'    => 'Optom Address',
        // 'value'     => 'Helper::convertMySQL2NHS($data->created_date)',
    ),
    array(
        'name'      => 'invoice_status_id',
        'header'    => 'Invoice status',
        'type'      => 'raw',
        'value'     => '$data->invoice_status->name'
       // 'value' => '$data->invoiceStatusSelect( $data->invoice_status_id )'

    ),
    array(
        'id'        => 'comment',
        'name'      => 'comment',
        'type'      => 'raw',
        'value'     => '$data->comment',
        'header'    => 'Comment',
    ),
    array(
        'id'                    => 'actions',
        'header'                => 'Actions',
        'class'                 => 'CButtonColumn',
        'htmlOptions'           => array('class' => 'left'),
        'template'              => '<span style="white-space: nowrap;">{view} {edit}</span>',
        'viewButtonImageUrl'    => false,
        'buttons' => array(
            'view' => array(
                'options'   => array('title' => 'View CVI', 'class' => ''),
                'url'       => 'Yii::app()->createURL("/OphCiExamination/default/view/", array("id" => $data->event_id))',
                'label'     => '<button class="secondary small">View</button>'
            ),
            'edit' => array(
                'options'   => array('title' => 'Edit'),
                'url'       => 'Yii::app()->createURL("/OphCiExamination/OptomFeedback/update/", array("id" => $data->event_id))',
                'label'     => '<button type="button" id="edit-optom-row" class="secondary small ajax-button">Edit</button>',
            ),
        ),
    )
);

?>

<h1 class="badge">Optometrist Feedback Manager</h1>
<div class="box content">
    <div class="row">
        <div class="large-12 column">
            <div class="box generic">
                <h1>Optometrist Feedback Manager</h1>
                <?php $this->renderPartial('/optom/list_filter', array('list_filter' => $list_filter)) ?>

                <div class="panel">
                    <?php
                        $this->widget('zii.widgets.grid.CGridView', array(
                            'itemsCssClass' => 'grid',
                            'dataProvider' => $dp,
                           // 'filter' => $model,
                            'htmlOptions' => array('id' => 'inbox-table'),
                            'summaryText' => '<small> {start}-{end} of {count} </small>',
                            'columns' => $cols,
                        ));
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>