	<div class="action_options">
		<?php if (@$editing) {?>
			<span class="aBtn view-event"><a class="view-event" href="/patient/event/<?php echo $event->id?>">View</a></span><span class="aBtn_inactive">Edit</span>
		<?php }else{?>
			<span class="aBtn_inactive">View</span><span class="aBtn edit-event"<?php if (!$editable) {?> style="display: none;"<?php }?>><a class="edit-event" href="/clinical/update/<?php if (is_object($event)) echo $event->id?>?patient_id=<?php echo $this->patient->id?>">Edit</a></span>
		<?php }?>
	</div>
