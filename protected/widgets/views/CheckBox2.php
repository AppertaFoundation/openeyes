<?php if (!@$options['nowrapper']) {?>
	<div id="div_<?php echo get_class($element)?>_<?php echo $field?>" class="eventDetail">
		<div class="label"><?php echo CHtml::encode($element->getAttributeLabel($field))?>:</div>
		<div class="datacol1">
			<?php }?>
			<?php echo CHtml::hiddenField(get_class($element)."[$field]",'0')?>
			<?php echo CHtml::checkBox(get_class($element)."[$field]",$checked[$field])?>
		<?php if (!@$options['nowrapper']) {?>
		</div>
	</div>
<?php }?>
