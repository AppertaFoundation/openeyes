<?php

$cols = array(
    array(
        'id' => 'created_date',
        'name' => 'created_date',
        'type' => 'raw',
        'header' => 'Date Received',
        'value' => 'Helper::convertMySQL2NHS($data->created_date)',
    ),
    array(
        'class' => 'CLinkColumn',
        'header' => 'Patient',
        'urlExpression' => 'Yii::app()->createURL("/patient/view/", array("id" => $data->event->episode->patient_id))',
        'labelExpression' => function ($data) {
            $institution = Institution::model()->getCurrent();
            $primary_identifier = PatientIdentifierHelper::getIdentifierForPatient(
                SettingMetadata::model()->getSetting('display_primary_number_usage_code'),
                $data->event->episode->patient->id,
                $institution->id,
                $this->selectedSiteId
            );
            $patient_identifier_widget = $this->widget(
                'application.widgets.PatientIdentifiers',
                ['patient' => $data->event->episode->patient, 'show_all' => true, 'tooltip_size' => 'small'],
                true
            );
            return $data->event->episode->patient->getDisplayName() .
                "<br>  (" . PatientIdentifierHelper::getIdentifierValue($primary_identifier) . ")" .
                $patient_identifier_widget;
        },
    ),
    array(
        'id' => 'optometrist',
        'name' => 'optometrist',
        'type' => 'raw',
        'header' => 'Optometrist Name',
        'value' => '$data->optometrist',
    ),
    array(
        'id' => 'optometrist_address',
        'name' => 'optometrist_address',
        'type' => 'raw',
        'header' => 'Optom Address',
        'value' => '$data->optometrist_address',
    ),
    array(
        'name' => 'invoice_status_id',
        'header' => 'Invoice Status',
        'type' => 'raw',
        'value' => '$data->invoiceStatusSelect( $data->invoice_status_id )',
        'htmlOptions' => array('class' => 'editable-select')

    ),
    array(
        'id' => 'optom-comment',
        'name' => 'comment',
        'type' => 'raw',
        'header' => 'Comment',
        'value' => 'CHtml::textArea( "comment", $data->comment)',
    ),
    array(
        'id' => 'actions',
        'header' => 'Actions',
        'class' => 'CButtonColumn',
        'htmlOptions' => array('class' => 'left'),
        'template' => '<span style="white-space: nowrap;">{view} {save} <br> {log}</span>',
        'viewButtonImageUrl' => false,
        'buttons' => array(
            'view' => array(
                'options' => array('title' => 'View examination'),
                'url' => 'Yii::app()->createURL("/OphCiExamination/default/view/", array("id" => $data->event_id))',
                'label' => '<button  class="secondary small">View</button>'
            ),
            'save' => array(
                'options' => array('title' => 'Save', 'data-id' => '$data->id'),
                'url' => '',
                'label' => '<button type="button" class="edit-optom-row secondary small ajax-button">Save</button>'
            ),
            'log' => array(
                'options' => array('title' => 'View log', 'data-id' => '$data->id', 'style' => 'margin-top:10px; display:block; text-align:center;'),
                'url' => '',
                'label' => '<button type="button" class="view-audit-log primary small ajax-button" style="display:block; width: 100%;">View log</button>'
            ),

        ),
    )
);

?>
<script type="text/javascript">
    $(document).ready(function () {

        $('.edit-optom-row').click(function () {
            var row = $(this).closest('tr');
            var td = $(this).closest('td');
            var rowID = row.attr('id');

            var data = {};
            row.find('input,select,textarea').each(function () {
                data[$(this).attr('name')] = $(this).val();
            });
            data['id'] = rowID;
            data['YII_CSRF_TOKEN'] = YII_CSRF_TOKEN;

            $.ajax({
                'type': 'POST',
                'data': data,
                'url': baseUrl + '/OphCiExamination/OptomFeedback/optomAjaxEdit/' + rowID,
                'dataType': 'json',
                'success': function (resp) {

                    if (resp.s == 1) {

                        div = '<div id="flash-success" class="optom-ajax-msg alert-box with-icon info">' + resp.msg + '</div>';
                        row.append(div);
                        setTimeout(function () {
                            $(".optom-ajax-msg").hide(300)
                        }, 2000);

                    } else {
                        new OpenEyes.UI.Dialog.Alert({
                            content: resp.msg
                        }).open();
                    }
                },
                'error': function (resp, status, error) {
                    new OpenEyes.UI.Dialog.Alert({
                        content: "Something went wrong " + status + ": " + error
                    }).open();
                }
            });

        });

        $('.view-audit-log').click(function () {
            var row = $(this).closest('tr');
            var rowID = row.attr('id');

            var data = {};
            data['YII_CSRF_TOKEN'] = YII_CSRF_TOKEN;

            $.ajax({
                'type': 'POST',
                'data': data,
                'url': baseUrl + '/OphCiExamination/OptomFeedback/getAuditEventLog/' + rowID,
                'success': function (resp) {
                    new OpenEyes.UI.Dialog({
                        title: 'Event log',
                        content: resp,
                        dialogClass: 'dialog event',
                        width: "90%",
                    }).open();

                },
                'error': function (resp, status, error) {
                    new OpenEyes.UI.Dialog.Alert({
                        content: "Something went wrong " + status + ": " + error
                    }).open();
                }
            });

        });
    });

</script>


<div class="oe-full-header flex-layout">
    <div class="title wordcaps">Optometrist Feedback Manager</div>
    <div>
        <!-- no header buttons -->
    </div>
</div>
<div class="oe-full-content subgrid oe-audit">
    <nav class="oe-full-side-panel audit-filters">
        <?php $this->renderPartial('/optom/list_filter', array('list_filter' => $list_filter)) ?>
    </nav>
    <main id="searchResults" class="oe-full-main audit-main">
        <?php
        $this->widget('zii.widgets.grid.CGridView', array(
            'itemsCssClass' => 'standard',
            'rowHtmlOptionsExpression' => 'array("id" => $data->id, "class" => "optom-result-row")',
            'dataProvider' => $dp,
            'htmlOptions' => array('id' => 'inbox-table'),
            'summaryText' => '<small> {start}-{end} of {count} </small>',
            'columns' => $cols,
        ));
        ?>
    </main>
</div>
