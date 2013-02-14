<?php
$event = $this->event;
$event_type = $event->eventType->name;
?>
<div class="metaData">
	<span class="info"><?php echo $event_type ?> created by <span class="user"><?php echo $event->user->fullname ?></span>
		on <?php echo $event->NHSDate('created_date') ?>
		at <?php echo date('H:i', strtotime($event->created_date)) ?></span>
	<span class="info"><?php echo $event_type ?> last modified by <span class="user"><?php echo $event->usermodified->fullname ?></span>
		on <?php echo $event->NHSDate('last_modified_date') ?>
		at <?php echo date('H:i', strtotime($event->last_modified_date)) ?></span>
</div>
