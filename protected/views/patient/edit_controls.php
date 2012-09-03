	<div class="action_options">
		<?php if (@$this->editing) {
			if ($this->event->eventType->class_name == 'OphTrOperation') {?>
				You can: <span class="aBtn view-event"><?php echo CHtml::link('View',array('/patient/event/'.$this->event->id),array('class'=>"view-event"))?></span><span class="aBtn_inactive">Edit</span>
			<?php }else{?>
				You can: <span class="aBtn view-event"><?php echo CHtml::link('View',array('/'.$this->event->eventType->class_name.'/default/view/'.$this->event->id),array('class'=>"view-event"))?></span><span class="aBtn_inactive">Edit</span>
			<?php }?>
		<?php }else{
			if (is_object($this->event)) {
				if ($this->event->eventType->class_name == 'OphTrOperation') {?>
					You can: <span class="aBtn_inactive">View</span><span class="aBtn edit-event"<?php if (!$this->editable) {?> style="display: none;"<?php }?>><?php echo CHtml::link('Edit',array('/clinical/update/'.$this->event->id),array('class'=>"edit-event"))?></span><?php if ($this->event->canDelete()) {?><span class="aBtn edit-event"><?php echo CHtml::link('Delete',array('/clinical/deleteevent/'.$this->event->id),array('class'=>"delete-event"))?></span><?php }?>
				<?php }else{?>
					You can: <span class="aBtn_inactive">View</span><span class="aBtn edit-event"<?php if (!$this->editable) {?> style="display: none;"<?php }?>><?php echo CHtml::link('Edit',array('/'.$this->event->eventType->class_name.'/default/update/'.$this->event->id),array('class'=>"edit-event"))?></span><?php if ($this->event->canDelete()) {?><span class="aBtn edit-event"><?php echo CHtml::link('Delete',array('/'.$this->event->eventType->class_name.'/default/delete/'.$this->event->id),array('class'=>"edit-event"))?></span><?php }?>
				<?php }
			}?>
		<?php }?>
	</div>
