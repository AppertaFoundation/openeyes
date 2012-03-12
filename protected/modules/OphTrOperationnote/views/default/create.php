<?php
$this->breadcrumbs=array(
	$this->module->id,
);
?>
<h1><?php echo $this->uniqueId . '/' . $this->action->id; ?></h1>

<p>
This is the default <?php echo $this->action->id; ?> view for the <?php echo get_class($this); ?> controller of the <?php echo $this->module->name ?> event type module.
</p>
<p>
You may customize this page by editing <tt><?php echo __FILE__; ?></tt>
</p>
<p>
We need to take a position on AJAX before proceeding much further.  We need to know whether this file needs to handle loading in of the element types, like 'patient/views/episodes.php' or rendering the whole page.  in which case rather than pushing in the element types it ought ot render the page based on a parent template then call an inherited function for grabbing in the element types and renderpartialing them.
</p>
