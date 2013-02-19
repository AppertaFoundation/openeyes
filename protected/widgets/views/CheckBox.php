<?php if (@$options['text-align'] == 'right') {?>
	<div id="div_<?php echo get_class($element)?>_<?php echo $field?>" class="eventDetail"<?php if (@$htmlOptions['hide']) {?> style="display: none;"<?php }?>>
		<?php if (!@$options['no-label']) {?>
			<div class="label"></div>
		<?php }?>
		<div class="data">
			<?php echo CHtml::hiddenField(get_class($element)."[$field]",'0')?>
			<?php echo CHtml::checkBox(get_class($element)."[$field]",$checked[$field],$htmlOptions)?>
			<?php echo $element->getAttributeLabel($field)?>
		</div>
	</div>
<?php }else {?>
	<?php if (!@$options['nowrapper']) {?>
		<div id="div_<?php echo get_class($element)?>_<?php echo $field?>" class="eventDetail"<?php if (@$htmlOptions['hide']) {?> style="display: none;"<?php }?>>
			<?php if (!@$options['no-label']) {?>
				<div class="label"><?php echo CHtml::encode($element->getAttributeLabel($field))?>:</div>
			<?php }?>
			<div class="datacol1">
				<?php }?>
				<?php echo CHtml::hiddenField(get_class($element)."[$field]",'0')?>
				<?php echo CHtml::checkBox(get_class($element)."[$field]",$checked[$field],$htmlOptions)?>
			<?php if (!@$options['nowrapper']) {?>
			</div>
		</div>
	<?php }?>
<?php }?>
