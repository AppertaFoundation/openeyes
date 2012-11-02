<?php if (!@$htmlOptions['nowrapper']) {?><div id="div_<?php echo get_class($element)?>_<?php echo $field?>_TextSelection" class="eventDetail">
	<div class="label"><?php echo $element->getAttributeLabel($field)?>:</div>
	<div class="data">
	<?php }?>
		<select class="dropDownTextSelection<?php if(@$htmlOptions['delimited']) { echo ' delimited'; } ?><?php if (isset($htmlOptions['class'])) echo ' '.$htmlOptions['class']?>" id="dropDownTextSelection_<?php echo get_class($element) ?>_<?php echo $field ?>">
			<?php if (isset($htmlOptions['empty'])) {?>
				<option value=""><?php echo $htmlOptions['empty']?></option>
			<?php }else{?>
				<option value="">- Please select -</option>
			<?php }?>
			<?php foreach ($options as $id => $content) {?>
				<option value="<?php echo $id?>"><?php echo $content?></option>
			<?php }?>
		</select>
		<?php if (!@$htmlOptions['nowrapper']) {?>
	</div>
</div>
<?php }?>
