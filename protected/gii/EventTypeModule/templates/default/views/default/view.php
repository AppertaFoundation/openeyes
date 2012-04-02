<?php echo '<?php ';?>
	$this->breadcrumbs=array($this->module->id);
	$this->header();
<?php echo '?>';?>

<h3 class="withEventIcon" style="background:transparent url(/img/_elements/icons/event/medium/treatment_operation_note.png) center left no-repeat;"><?php echo '<?php ';?> echo $this->event_type->name <?php echo '?>';?></h3>

<div>
	<?php echo '<?php ';?> $this->renderDefaultElements($this->action->id); <?php echo '?>';?>
	<?php echo '<?php ';?> $this->renderOptionalElements($this->action->id); <?php echo '?>';?>

	<div class="cleartall"></div>
</div>

<?php echo '<?php ';?> $this->footer();<?php echo '?>';?>
