<?php echo '<?php '; ?>
	$this->breadcrumbs=array($this->module->id);
	$this->header();
<?php echo '?>'; ?>

<h3 class="withEventIcon" style="background:transparent url(<?php echo '<?php '; ?>echo $this->imgPath<?php echo '?>';?>medium.png) center left no-repeat;"><?php echo '<?php ';?>echo $this->event_type->name <?php echo '?>';?></h3>

<div>
	<?php echo '<?php ';?>
		$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
			'id'=>'clinical-create',
			'enableAjaxValidation'=>false,
			'htmlOptions' => array('class'=>'sliding'),
			'focus'=>'#procedure_id'
		));
	<?php echo '?>';?>

	<?php echo '<?php ';?> $this->displayErrors($errors)<?php echo '?>';?>

	<?php echo '<?php ';?> $this->renderDefaultElements($this->action->id, $form); <?php echo '?>';?>

	<?php echo '<?php ';?> $this->renderOptionalElements($this->action->id, $form); <?php echo '?>';?>

	<?php echo '<?php ';?> $this->displayErrors($errors)<?php echo '?>';?>

		<div class="cleartall"></div>
		<div class="form_button">
			<img class="loader" style="display: none;" src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" alt="loading..." />&nbsp;
			<button type="submit" class="classy green venti" id="et_save" name="save"><span class="button-span button-span-green">Save</span></button>
			<button type="submit" class="classy red venti" id="et_cancel" name="cancel"><span class="button-span button-span-red">Cancel</span></button>
		</div>
	<?php echo '<?php ';?> $this->endWidget(); <?php echo '?>';?>
</div>

<?php echo '<?php ';?> $this->footer(); <?php echo '?>';?>
