	<div class="action_options">
		<?php if (@$this->editing) {
			if ($this->event->eventType->class_name == 'OphTrOperation') {?>
				You can: <span class="aBtn view-event"><a class="view-event" href="<?php echo Yii::app()->createUrl('patient/event/'.$this->event->id)?>">View</a></span><span class="aBtn_inactive">Edit</span>
			<?php }else{?>
				You can: <span class="aBtn view-event"><a class="view-event" href="<?php echo Yii::app()->createUrl($this->event->eventType->class_name.'/Default/view/'.$this->event->id)?>">View</a></span><span class="aBtn_inactive">Edit</span>
			<?php }?>
		<?php }else{
			if (is_object($this->event)) {
				if ($this->event->eventType->class_name == 'OphTrOperation') {?>
					You can: <span class="aBtn_inactive">View</span><span class="aBtn edit-event"<?php if (!$this->editable) {?> style="display: none;"<?php }?>><a class="edit-event" href="<?php echo Yii::app()->createUrl('clinical/update/'.$this->event->id)?>">Edit</a></span><?php if ($this->event->canDelete()) {?><span class="aBtn edit-event"><a class="delete-event" href="<?php echo Yii::app()->createUrl('clinical/deleteevent/'.$this->event->id)?>">Delete</a></span><?php }?>
				<?php }else{?>
					You can: <span class="aBtn_inactive">View</span><span class="aBtn edit-event"<?php if (!$this->editable) {?> style="display: none;"<?php }?>><a class="edit-event" href="<?php echo Yii::app()->createUrl($this->event->eventType->class_name.'/Default/update/'.$this->event->id)?>">Edit</a></span><?php if ($this->event->canDelete()) {?><span class="aBtn edit-event"><a class="edit-event" href="<?php echo Yii::app()->createUrl($this->event->eventType->class_name.'/Default/delete/'.$this->event->id)?>">Delete</a></span><?php }?>
				<?php }
			}?>
		<?php }?>
	</div>
