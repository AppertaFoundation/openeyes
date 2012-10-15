<div id="div_<?php echo get_class($element)?>_<?php echo $field?>_TextSelection" class="eventDetail">
	<?php if (!@$htmlOptions['no_label']) {?>
		<div class="label"><?php echo $element->getAttributeLabel($field)?>:</div>
	<?php }?>
	<div class="data">
		<?php foreach ($options as $name => $data) {?>
			<select class="dropDownTextSelection<?php if (isset($htmlOptions['class'])) {?> <?php echo $htmlOptions['class']?><?php }?>" id="dropDownTextSelection_<?php echo get_class($element)?>_<?php echo $field?>"<?php if (@$htmlOptions['remove_selections'] === false) {?> data-remove-selections="false"<?php }?>>
				<?php if (isset($data['empty'])) {?>
					<option value=""><?php echo $data['empty']?></option>
				<?php }else{?>
					<option value="">- Please select -</option>
				<?php }?>
				<?php foreach ($data['options'] as $id => $content) {?>
					<option value="<?php echo $id?>"><?php echo $content?></option>
				<?php }?>
			</select>
		<?php }?>
	</div>
</div>
