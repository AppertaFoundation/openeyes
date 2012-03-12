<?php
$this->breadcrumbs=array(
	$this->module->id,
);
?>
<h1><?php echo $this->uniqueId . '/' . $this->action->id; ?></h1>

<p>
This is the default <?php echo $this->action->id; ?> view for the <?php echo get_class($this); ?> controller of the <?php echo $this->module->name ?> event type module.
You can customise this page by editing <tt><?php echo __FILE__; ?></tt>
</p>

<?php $this->renderDefaultElements($this->action->id); ?>
<?php $this->renderOptionalElements($this->action->id); ?>
