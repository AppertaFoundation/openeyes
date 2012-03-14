	<div class="action_options">
		<span class="aBtn_inactive">View</span><span class="aBtn edit-event"<?php if (!$editable) {?> style="display: none;"<?php }?>><a class="edit-event" href="/clinical/update/<?php if (is_object($event)) echo $event->id?>?patient_id=<?php echo $patient->id?>">Edit</a></span>
	</div>
	<div class="action_options_alt" style="display: none;">
		<span class="aBtn save"><a href="#" class="edit-save">Save</a></span><span class="aBtn cancel"><a href="#" class="edit-cancel">Cancel</a></span>
	</div>
