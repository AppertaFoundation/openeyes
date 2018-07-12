<?php
if (empty($patient)) {
    $patient = $this->patient;
}
?>
<div class="row field-row">
    <div class="large-2 column">
        <label>Attachments:</label>
    </div>
    <div class="large-10 column end">
        <table id="correspondence_attachments_table">
            <thead>
            <tr>
                <th>Attachment type</th>
                <th>Title</th>
                <th>Event Date</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <tr id="correspondence_attachments_table_last_row" data-id="1">
                <td colspan="2">
                <td>
                <td>
                    <?php

                    $events = $this->getAttachableEvents($patient);
                    ?>
                    <?= CHtml::dropDownList(
                        'attachment_events',
                        ' ',
                        CHtml::listData($events, 'id', function ($events) {
                            $event_type_name = $events->eventType->name;
                            $document_model = Element_OphCoDocument_Document::model()->findByAttributes(["event_id" => $events->id]);
                            $event_type_name = isset($document_model->sub_type) ? $document_model->sub_type->name : '';
                            return CHtml::encode($event_type_name . ' - ' . Helper::convertDate2NHS($events->event_date));
                        }), array('empty' => '- Select -'));
                    ?>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
