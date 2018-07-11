<tr data-id="">
    <input class="attachments_event_id" value="<?= $event->id ?>" type="hidden">
    <td><?php $document_model = Element_ophcodocument_document::model()->findByAttributes(["event_id" => $event->id]);
            echo isset($document_model->sub_type) ? $document_model->sub_type->name : ''; ?></td>
    <td><input type="text" class="attachments_display_title" name="attachments_display_title[]"   value="<?= $event->eventType->name ?>" /></td>
    <td><?= Helper::convertDate2NHS($event->event_date) ?></td>
    <td><button class="button small warning remove">remove</button></td>
</tr>
