	<div class="action_options">
		<?php if (@$this->editing) {
			if ($this->event_type->class_name == 'OphTrOperation') {?>
				<span class="aBtn view-event"><a class="view-event" href="/patient/event/<?php echo $this->event->id?>">View</a></span><span class="aBtn_inactive">Edit</span>
			<?php }else{?>
				<span class="aBtn view-event"><a class="view-event" href="/<?php echo $this->event_type->class_name?>/Default/view/<?php echo $this->event->id?>">View</a></span><span class="aBtn_inactive">Edit</span>
			<?php }?>
		<?php }else{
			if (is_object($this->event)) {
				if ($this->event_type->class_name == 'OphTrOperation') {?>
					<span class="aBtn_inactive">View</span><span class="aBtn edit-event"<?php if (!$this->editable) {?> style="display: none;"<?php }?>><a class="edit-event" href="/clinical/update/<?php echo $this->event->id?>">Edit</a></span>
				<?php }else{?>
					<span class="aBtn_inactive">View</span><span class="aBtn edit-event"<?php if (!$this->editable) {?> style="display: none;"<?php }?>><a class="edit-event" href="/<?php echo $this->event_type->class_name?>/Default/update/<?php echo $this->event->id?>">Edit</a></span>
				<?php }
			}?>
		<?php }?>
	</div>
