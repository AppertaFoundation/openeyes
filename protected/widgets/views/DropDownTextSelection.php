<div id="div_<?php echo get_class($element)?>_<?php echo $field?>" class="eventDetail">
	<div class="label"><?php echo $element->getAttributeLabel($field)?>:</div>
	<div class="data">
		<select class="dropDownTextSelection" id="dropDownTextSelection_<?php echo get_class($element)?>_<?php echo $field?>">
			<option value="">- Please select -</option>
			<?php foreach ($options as $id => $content) {?>
				<option value="<?php echo $id?>"><?php echo $content?></option>
			<?php }?>
		<?phph }?>
	</div>
</div>
