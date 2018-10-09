<tr data-id="">
    <td>
			<?php echo $this->getEventSubType($event); ?>
			<input class="attachments_event_id" value="<?= $event->id ?>" type="hidden">
		</td>
    <td><input type="text" class="attachments_display_title" name="attachments_display_title[]"
               value="<?php echo $this->getEventSubType($event); ?>"/></td>
    <td><?= Helper::convertDate2NHS($event->event_date) ?></td>
    <td>
			<i class="oe-i trash"></i>
    </td>
</tr>
