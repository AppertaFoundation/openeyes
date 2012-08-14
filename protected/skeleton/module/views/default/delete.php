<?php
	$this->breadcrumbs=array($this->module->id);
	$this->header();
?>

<h3 class="withEventIcon" style="background:transparent url(<?php echo $this->imgPath?>medium.png) center left no-repeat;"><?php echo $this->event_type->name ?></h3>

<div>
	<div class="cleartall"></div>
</div>

<div id="delete_event">
	<h1>Delete event</h1>
	<div class="alertBox" style="margin-top: 10px;">
		<strong>WARNING: This will permanently delete the event and remove it from view.<br><br>THIS ACTION CANNOT BE UNDONE.</strong>
	</div>
	<p>
		<strong>Are you sure you want to proceed?</strong>
	</p>
	<?php
	echo CHtml::form(array('default/delete/'.$this->event->id), 'post', array('id' => 'deleteForm'));
		echo CHtml::hiddenField('event_id', $this->event->id); ?>
		<div class="buttonwrapper">
			<button type="submit" class="classy red venti" id="et_deleteevent"><span class="button-span button-span-red">Delete event</span></button>
			<button type="submit" class="classy green venti" id="et_canceldelete"><span class="button-span button-span-green">Cancel</span></button>
			<img class="loader" src="/img/ajax-loader.gif" alt="loading..." style="display: none;" />
		</div>
	<?php echo CHtml::endForm(); ?>
</div>

<?php $this->footer() ?>
