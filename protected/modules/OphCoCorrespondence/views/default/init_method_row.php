<tr data-id="">
    <td>
            <?php echo $this->getEventSubType($event); ?>
            <input class="attachments_event_id" value="<?= $event->id ?>" type="hidden"/>
            <input class="attachments_event_class_name" value="<?= $event->eventType->class_name ?>" type="hidden"/>
        </td>
    <td><input type="text" class="attachments_display_title" name="attachments_display_title[]"
               value="<?php echo $this->getEventSubType($event); ?>"/></td>
    <td><?= Helper::convertDate2NHS($event->event_date) ?></td>
    <td class="attachment_status">
        <i class="oe-i waiting small pad-right"></i>
        Pending...
    </td>
    <td style="text-align: right;">
            <button class="reprocess_btn" style="display: none" type="button">Try again</button> &nbsp;
            <i class="oe-i trash"></i>
    </td>
</tr>
