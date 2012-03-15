<?php
	$this->breadcrumbs=array($this->module->id);
	$this->header();
?>

<h3 class="withEventIcon" style="background:transparent url(/img/_elements/icons/event/medium/treatment_operation_note.png) center left no-repeat;"><?php echo $event_type->name ?></h3>

<div>
	<?php
		$form = $this->beginWidget('CActiveForm', array(
			'id'=>'clinical-create',
			'enableAjaxValidation'=>false,
			'htmlOptions' => array('class'=>'sliding'),
			'focus'=>'#procedure_id'
		));
	?>

		<?php $this->renderDefaultElements($this->action->id); ?>
		<?php $this->renderOptionalElements($this->action->id); ?>

		<?php $this->displayErrors($errors)?>

		<div class="cleartall"></div>
		<div class="form_button">
			<img class="loader" style="display: none;" src="/img/ajax-loader.gif" alt="loading..." />&nbsp;
			<button type="submit" class="classy green venti" id="saveOperation" name="saveOperation"><span class="button-span button-span-green">Save</span></button>
			<button type="submit" class="classy red venti" id="cancelOperation" name="cancelOperation"><span class="button-span button-span-red">Cancel</span></button>
		</div>
	<?php $this->endWidget(); ?>
</div>

<?php $this->footer() ?>
