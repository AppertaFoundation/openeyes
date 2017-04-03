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
    ),
    array(
        'name'      => 'invoice_status_id',
        'header'    => 'Invoice status',
        'type'      => 'raw',
        //'value'     => '$data->invoice_status->name',
        'value' => '$data->invoiceStatusSelect( $data->invoice_status_id )',
        'htmlOptions'   => array('class' => 'editable-select')

    ),
    array(
        'id'            => 'optom-comment',
        'name'          => 'comment',
        'type'          => 'raw',
        //'value'         => '$data->comment',
        'header'        => 'Comment',
        'value'         => 'CHtml::textArea( "comment", $data->comment)',
        //'htmlOptions'   => array('class' => 'editable-text'),
    ),
    array(
        'id'                    => 'actions',
        'header'                => 'Actions',
        'class'                 => 'CButtonColumn',
        'htmlOptions'           => array('class' => 'left'),
        'template'              => '<span style="white-space: nowrap;">{view} {save}</span>',
        'viewButtonImageUrl'    => false,
        'buttons' => array(
            'view' => array(
                'options'   => array('title' => 'View CVI', 'class' => ''),
                'url'       => 'Yii::app()->createURL("/OphCiExamination/default/view/", array("id" => $data->event_id))',
                'label'     => '<button  class="secondary small">View</button>'
            ),
            'save' => array(
                'options'   => array('title' => 'Save', 'data-id' => '$data->id'),
                'url'       => '',
                'label'     => '<button type="button" class="edit-optom-row secondary small ajax-button">Save</button>'
            ),

        ),
    )
);

?>
<script type="text/javascript">
   $(document).ready(function() {
       $('.edit-optom-row').click(function(){
           var row = $(this).closest('tr');
           var td = $(this).closest('td');
           var rowID = row.attr('id');

           var data = {};
           row.find('input,select,textarea').each(function(){
               data[$(this).attr('name')]=$(this).val();
           });
           data['id'] = rowID;
           data['YII_CSRF_TOKEN'] = YII_CSRF_TOKEN;


           $.ajax({
               'type': 'POST',
               'data': data,
               'url': baseUrl+'/OphCiExamination/OptomFeedback/optomAjaxEdit/'+rowID,
               'dataType': 'json',
               'success': function(resp) {

                    if(resp.s == 1){

                        div = '<div id="flash-success" class="optom-ajax-msg alert-box with-icon info">'+ resp.msg +'</div>';
                        td.append(div);
                        setTimeout(function() {
                            $(".optom-ajax-msg").hide(300)
                        }, 2000);

                    } else {
                        new OpenEyes.UI.Dialog.Alert({
                            content:  resp.msg}).open();
                    }
               },
               'error': function(resp, status, error) {
                   new OpenEyes.UI.Dialog.Alert({
                       content: "Something went wrong " + status + ": " + error}).open();
               }
           });

       });
   });

</script>

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
                            'rowHtmlOptionsExpression' => 'array("id" => $data->id)',
                            'dataProvider' => $dp,
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